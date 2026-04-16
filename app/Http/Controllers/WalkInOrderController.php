<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\LogsActivity;
use App\Http\Requests\StoreWalkInRequest;
use App\Traits\ManagesInventory;
use App\Models\Setting;

class WalkInOrderController extends Controller
{
    use LogsActivity, ManagesInventory;

    public function __construct()
    {
        $this->middleware('auth:web,client');
    }

    public function store(StoreWalkInRequest $request)
    {
        $officeId = $request->office_id;

        $isImmediate = $request->pickup_type === 'now';

        $order = Order::create([
            'first_name' => $request->first_name ?? 'Walk-in',
            'last_name' => $request->last_name ?? 'Customer',
            'client_name' => $request->client_name ?? (($request->first_name && $request->last_name) ? "{$request->first_name} {$request->last_name}" : 'Walk-in Customer'),
            'customer_type' => $request->customer_type,
            'pr_number' => $request->pr_number,
            'budget_code' => $request->budget_code,
            'office_id' => $officeId,
            'other_location' => $request->other_location,
            'contact_number' => 'N/A (Walk-in)',
            'quantity' => $request->quantity,
            'total_amount' => $request->quantity * Setting::getUnitPrice(),
            'delivery_date' => Carbon::today(),
            'remarks' => $isImmediate ? 'Walk-in (Picked up Now)' : 'Walk-in (Pick up Later)',
            'order_type' => 'walk-in',
            'status' => $isImmediate ? 'completed' : 'confirmed',
            'delivered_at' => $isImmediate ? now() : null,
            'is_refill' => $request->boolean('is_refill'),
            'missing_caps_count' => $request->missing_caps_count ?? 0,
        ]);

        $this->deductOrderStock($order);

        $message = $isImmediate 
            ? 'Walk-in order completed successfully!' 
            : 'Walk-in order confirmed for later pickup.';

        return back()->with('success', $message);
    }
}
