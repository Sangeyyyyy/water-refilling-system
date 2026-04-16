<?php

namespace App\Traits;

use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

trait ManagesInventory
{
    /**
     * Deduct stock and update gallon rotation for an order.
     */
    protected function deductOrderStock(Order $order)
    {
        // Don't deduct if already done
        if ($order->inventory_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $cap = Inventory::where('slug', 'cap')->first();
            $gallon = Inventory::where('slug', 'gallon')->first();

            // 1. Handle Caps
            if ($order->is_refill) {
                // For refills, only deduct if there are missing caps
                if ($order->missing_caps_count > 0 && $cap) {
                    $cap->decrement('stock_level', $order->missing_caps_count);
                    $this->logActivity('Inventory Adjusted', "Deducted {$order->missing_caps_count} caps for missing returns on order #{$order->id}.", ['order_id' => $order->id, 'item_id' => $cap->id]);
                }
            } else {
                // For new gallons, always deduct one cap per gallon
                if ($cap) {
                    $cap->decrement('stock_level', $order->quantity);
                    $this->logActivity('Inventory Adjusted', "Deducted {$order->quantity} caps for new gallon order #{$order->id}.", ['order_id' => $order->id, 'item_id' => $cap->id]);
                }
            }

            // 2. Handle Gallons (Inventory Level & Rotation Tracking)
            if (!$order->is_refill) {
                // Deduct physical gallon stock
                if ($gallon) {
                    $gallon->decrement('stock_level', $order->quantity);
                    $this->logActivity('Inventory Adjusted', "Deducted {$order->quantity} gallons for new gallon order #{$order->id}.", ['order_id' => $order->id, 'item_id' => $gallon->id]);
                }

                // Update rotation tracker (who has these gallons?)
                if ($order->office_id) {
                    $order->office->increment('gallon_count', $order->quantity);
                } elseif ($order->client_id) {
                    $client = $order->client;
                    if ($client && $client->office_id) {
                        $client->office->increment('gallon_count', $order->quantity);
                    } elseif ($client) {
                        $client->increment('gallon_count', $order->quantity);
                    }
                } elseif ($order->user_id) {
                    $order->user->increment('gallon_count', $order->quantity);
                }
            }

            // 3. Mark as deducted
            $order->update(['inventory_deducted' => true]);
            return true;
        });
    }

    /**
     * Return stock to inventory (for cancellations).
     */
    protected function returnOrderStock(Order $order)
    {
        if (!$order->inventory_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $cap = Inventory::where('slug', 'cap')->first();
            $gallon = Inventory::where('slug', 'gallon')->first();

            // 1. Return Caps
            if ($order->is_refill) {
                if ($order->missing_caps_count > 0 && $cap) {
                    $cap->increment('stock_level', $order->missing_caps_count);
                }
            } else {
                if ($cap) {
                    $cap->increment('stock_level', $order->quantity);
                }
            }

            // 2. Return Gallons
            if (!$order->is_refill) {
                if ($gallon) {
                    $gallon->increment('stock_level', $order->quantity);
                }

                // Decrement rotation tracker
                if ($order->office_id && $order->office) {
                    $order->office->decrement('gallon_count', $order->quantity);
                } elseif ($order->client_id) {
                    $client = $order->client;
                    if ($client && $client->office_id && $client->office) {
                        $client->office->decrement('gallon_count', $order->quantity);
                    } elseif ($client) {
                        $client->decrement('gallon_count', $order->quantity);
                    }
                } elseif ($order->user_id && $order->user) {
                    $order->user->decrement('gallon_count', $order->quantity);
                }
            }

            $order->update(['inventory_deducted' => false]);
            return true;
        });
    }

    /**
     * Deduct PPMP budget for an order.
     */
    protected function deductOrderBudget(Order $order)
    {
        if ($order->customer_type !== 'Office' || !$order->office_id) {
            return false;
        }

        // Don't deduct if already done
        if ($order->budget_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $ppmp = $order->ppmp_id 
                ? \App\Models\Ppmp::find($order->ppmp_id)
                : \App\Models\Ppmp::where('office_id', $order->office_id)
                    ->where('status', 'approved')
                    ->where('fiscal_year', $order->created_at->year)
                    ->first();

            if ($ppmp && $ppmp->remaining_budget >= $order->total_amount) {
                $ppmp->decrement('remaining_budget', $order->total_amount);
                $order->update(['budget_deducted' => true]);
                $this->logActivity('Budget Deducted', "Deducted ₱" . number_format($order->total_amount, 2) . " from PPMP for order #{$order->id}.", ['order_id' => $order->id, 'ppmp_id' => $ppmp->id]);
                return true;
            }

            return false;
        });
    }

    /**
     * Return PPMP budget for an order (for cancellations).
     */
    protected function returnOrderBudget(Order $order)
    {
        if ($order->customer_type !== 'Office' || !$order->office_id) {
            return false;
        }

        // Only return if it was actually deducted and not yet returned
        if (!$order->budget_deducted) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $ppmp = $order->ppmp_id 
                ? \App\Models\Ppmp::find($order->ppmp_id)
                : \App\Models\Ppmp::where('office_id', $order->office_id)
                    ->where('status', 'approved')
                    ->where('fiscal_year', $order->created_at->year)
                    ->first();

            if ($ppmp) {
                $ppmp->increment('remaining_budget', $order->total_amount);
                $order->update(['budget_deducted' => false]);
                $this->logActivity('Budget Returned', "Refunded ₱" . number_format($order->total_amount, 2) . " to PPMP for order #{$order->id}.", ['order_id' => $order->id, 'ppmp_id' => $ppmp->id]);
                return true;
            }

            return false;
        });
    }
}
