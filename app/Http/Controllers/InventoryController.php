<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use LogsActivity;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $items = Inventory::all();
        $inventory_alerts = Inventory::whereRaw('stock_level <= low_stock_threshold')->get();
        
        // Month stats for inventory context
        $stats = [
            'month_completed' => Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'completed')
                ->count(),
            'month_gallons' => Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->sum('quantity'),
        ];

        // Distribution Data (Gallon Rotation Tracking)
        $office_distribution = \App\Models\Office::with('division.collegeOffice.campus')->where('gallon_count', '>', 0)->orderBy('gallon_count', 'desc')->get();
        $user_distribution = \App\Models\User::where('gallon_count', '>', 0)->orderBy('gallon_count', 'desc')->get();
        $client_distribution = \App\Models\Client::where('gallon_count', '>', 0)->orderBy('gallon_count', 'desc')->get();

        $total_in_circulation = $office_distribution->sum('gallon_count') + 
                                $user_distribution->sum('gallon_count') + 
                                $client_distribution->sum('gallon_count');

        return view('admin.inventory', compact(
            'items', 
            'inventory_alerts', 
            'stats', 
            'office_distribution', 
            'user_distribution', 
            'client_distribution',
            'total_in_circulation'
        ));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, Inventory $inventory)
    {

        $request->validate([
            'stock_level' => 'required|integer',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'action' => 'required|in:add,set',
        ]);

        if ($request->action === 'add') {
            $inventory->increment('stock_level', $request->stock_level);
            $this->logActivity('Stock Added', "Added {$request->stock_level} {$inventory->unit} to {$inventory->item_name}.", ['item_id' => $inventory->id]);
        } else {
            $old_stock = $inventory->stock_level;
            $inventory->update(['stock_level' => $request->stock_level]);
            $this->logActivity('Stock Set', "Set {$inventory->item_name} stock from {$old_stock} to {$request->stock_level} {$inventory->unit}.", ['item_id' => $inventory->id]);
        }

        if ($request->filled('low_stock_threshold')) {
            $inventory->update(['low_stock_threshold' => $request->low_stock_threshold]);
            $this->logActivity('Threshold Updated', "Updated low stock threshold for {$inventory->item_name} to {$request->low_stock_threshold}.", ['item_id' => $inventory->id]);
        }

        return back()->with('success', "{$inventory->item_name} inventory updated successfully.");
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {

        $request->validate([
            'item_name' => 'required|string|max:255|unique:inventories,item_name',
            'stock_level' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $inventory = Inventory::create([
            'item_name' => $request->item_name,
            'slug' => \Illuminate\Support\Str::slug($request->item_name),
            'stock_level' => $request->stock_level,
            'unit' => $request->unit,
            'low_stock_threshold' => $request->low_stock_threshold ?? 100, // Default threshold
        ]);

        $this->logActivity('Item Created', "Created new inventory item: {$inventory->item_name} with initial stock of {$inventory->stock_level} {$inventory->unit}.", ['item_id' => $inventory->id]);

        return back()->with('success', 'New inventory item added successfully.');
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(Inventory $inventory)
    {

        $itemName = $inventory->item_name;
        $inventory->delete();

        $this->logActivity('Item Deleted', "Deleted inventory item: {$itemName}.");

        return back()->with('success', 'Inventory item deleted successfully.');
    }
}
