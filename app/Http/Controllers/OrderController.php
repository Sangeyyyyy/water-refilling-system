<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Office;
use App\Models\Ppmp;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\LogsActivity;
use App\Http\Requests\StoreOrderRequest;
use App\Traits\ManagesInventory;
use App\Models\Setting;

class OrderController extends Controller
{
    use LogsActivity, ManagesInventory;

    public function __construct()
    {
        $this->middleware('auth:web,client');
    }

    // Price per container is now managed in settings table

    public function create()
    {
        $offices = Office::with('division.campus')->orderBy('name')->get();
        $deliveryDates = $this->getNextDeliveryDates();
        $unitPrice = Setting::getUnitPrice();

        return view('orders.create', compact('offices', 'deliveryDates', 'unitPrice'));
    }

    public function store(StoreOrderRequest $request)
    {
        $user = auth('web')->user(); // Web guard (Admin/Staff)
        $client = auth('client')->user(); // Client guard

        // Fallback to Auth user/client data if fields are missing
        $authUser = $user ?? $client;
        $isOfficeRequest = $request->customer_type === 'Office';
        
        $firstName = $request->first_name ?: ($authUser ? $authUser->first_name : null);
        $lastName = $request->last_name ?: ($authUser ? $authUser->last_name : null);
        $officeId = $isOfficeRequest ? ($request->office_id ?: ($authUser ? $authUser->office_id : null)) : null;
        $contactNumber = $request->contact_number ?: ($authUser ? $authUser->contact_number : null);

        // Double check if delivery date is valid based on settings
        $date = Carbon::parse($request->delivery_date);
        $allowedDays = json_decode(Setting::get('delivery_days', json_encode(config('settings.delivery_days', ['Tuesday', 'Friday']))), true);
        
        if (!in_array($date->format('l'), $allowedDays)) {
            return back()->withErrors(['delivery_date' => 'Delivery must be on one of the following days: ' . implode(', ', $allowedDays)]);
        }
        
        // DNSC Container Ownership Validation
        if ($request->boolean('is_refill') && $request->container_ownership === 'dnsc') {
            $currentGallonCount = 0;
            if ($isOfficeRequest && $officeId) {
                $office = \App\Models\Office::find($officeId);
                $currentGallonCount = $office ? $office->gallon_count : 0;
            } elseif ($client) {
                $currentGallonCount = $client->gallon_count;
            } elseif ($user) {
                $currentGallonCount = $user->gallon_count;
            }

            if ($currentGallonCount < $request->quantity) {
                return back()->withInput()->withErrors(['quantity' => "You only have {$currentGallonCount} DNSC containers currently borrowed. Please adjust your quantity or select 'Personal Container'."]);
            }
        }

        // PPMP Check for Office Orders
        $totalAmount = $request->quantity * Setting::getUnitPrice();
        $ppmp = null;
        if ($request->customer_type === 'Office' && ($officeId || $request->ppmp_id)) {
            if ($request->ppmp_id) {
                $ppmp = Ppmp::where('id', $request->ppmp_id)
                    ->where('status', 'approved')
                    ->first();
            }

            // Fallback (or if ppmp_id not provided but budget_code is)
            if (!$ppmp && $request->budget_code && $officeId) {
                $ppmp = Ppmp::where('office_id', $officeId)
                    ->where('budget_code', $request->budget_code)
                    ->where('status', 'approved')
                    ->orderBy('fiscal_year', 'desc')
                    ->first();
            }
            
            if ($ppmp) {
                if ($ppmp->remaining_budget < $totalAmount) {
                    $codeMsg = $ppmp->budget_code ? " ({$ppmp->budget_code})" : "";
                    return back()->withInput()->withErrors(['quantity' => "Insufficient PPMP budget{$codeMsg}. Remaining: ₱" . number_format($ppmp->remaining_budget, 2)]);
                }
            } elseif ($request->ppmp_id || $request->budget_code) {
                return back()->withInput()->withErrors(['budget_code' => 'The selected budget code does not have an approved PPMP for this office.']);
            }
        }

        $order = \Illuminate\Support\Facades\DB::transaction(function() use ($request, $user, $client, $firstName, $lastName, $officeId, $contactNumber, $totalAmount, $ppmp) {
            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'client_id' => $client ? $client->id : null,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'client_name' => "{$firstName} {$lastName}",
                'customer_type' => $request->customer_type,
                'pr_number' => $request->customer_type === 'Office' ? $request->pr_number : null,
                'budget_code' => $request->customer_type === 'Office' ? $request->budget_code : null,
                'ppmp_id' => $request->customer_type === 'Office' ? $request->ppmp_id : null,
                'office_id' => $officeId,
                'other_location' => $request->other_location,
                'contact_number' => $contactNumber,
                'quantity' => $request->quantity,
                'total_amount' => $totalAmount,
                'delivery_date' => $request->delivery_date,
                'remarks' => $request->remarks,
                'order_type' => 'online',
                'status' => 'confirmed',
                'is_refill' => $request->boolean('is_refill'),
                'container_ownership' => $request->boolean('is_refill') ? $request->container_ownership : 'dnsc',
                'missing_caps_count' => $request->missing_caps_count ?? 0,
            ]);

            // Deduct inventory immediately for confirmed orders
            $this->deductOrderStock($order);

            // Deduct from PPMP if applicable
            $this->deductOrderBudget($order);

            return $order;
        });

        $this->logActivity('Online Order', "Placed an online order (#{$order->id}) for {$order->client_name} ({$order->quantity} units).", ['order_id' => $order->id]);


        return redirect()->route('orders.success', $order->id);
    }

    public function success(Order $order)
    {
        return view('orders.success', compact('order'));
    }

    private function getNextDeliveryDates($count = 6)
    {
        $dates = [];
        $date = Carbon::now();
        $allowedDays = json_decode(Setting::get('delivery_days', json_encode(config('settings.delivery_days', ['Tuesday', 'Friday']))), true);
        
        while (count($dates) < $count) {
            if (in_array($date->format('l'), $allowedDays)) {
                $dates[] = $date->copy();
            }
            $date->addDay();
        }

        return $dates;
    }
}
