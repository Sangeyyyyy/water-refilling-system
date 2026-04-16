<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\LogsActivity;
use App\Traits\ManagesInventory;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\BatchUpdateOrderStatusRequest;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    use LogsActivity, ManagesInventory;

    public function __construct()
    {
        $this->middleware('auth:web,client');
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->role, config('roles.admin_roles'))) {
            return redirect()->route('home');
        }

        $query = Order::with(['office.division.campus', 'client']);

        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($dateFrom) $query->whereDate('orders.created_at', '>=', $dateFrom);
        if ($dateTo) $query->whereDate('orders.created_at', '<=', $dateTo);

        if ($request->filled('status')) $query->where('orders.status', $request->status);
        if ($request->filled('customer_type')) $query->where('orders.customer_type', $request->customer_type);
        if ($request->filled('order_type')) $query->where('orders.order_type', $request->order_type);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $sort = $request->get('sort', 'desc');
        $orders = $query->orderBy('orders.created_at', $sort)->paginate(20)->withQueryString();

        return view('admin.history', compact('orders', 'dateFrom', 'dateTo'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $data = ['status' => $request->status];
        
        DB::transaction(function() use ($request, $order, &$data) {
            if ($request->status === 'completed' || $request->status === 'confirmed' || $request->status === 'out_for_delivery') {
                if ($request->status === 'completed') {
                    $data['delivered_at'] = now();
                }
                $this->deductOrderStock($order);
                $this->deductOrderBudget($order);
            } elseif (in_array($request->status, ['cancelled', 'rejected'])) {
                $this->returnOrderStock($order);
                $this->returnOrderBudget($order);
            }

            $order->update($data);
        });

        $this->logActivity('Order Status Updated', "Updated order #{$order->id} status to {$request->status}.", ['order_id' => $order->id, 'new_status' => $request->status]);

        return back()->with('success', "Order #{$order->id} status updated to " . ucfirst(str_replace('_', ' ', $request->status)) . ".");
    }

    public function batchUpdateStatus(BatchUpdateOrderStatusRequest $request)
    {
        $data = ['status' => $request->status];
        
        $count = 0;
        DB::transaction(function() use ($request, &$data, &$count) {
            if (in_array($request->status, ['completed', 'confirmed', 'out_for_delivery'])) {
                if ($request->status === 'completed') {
                    $data['delivered_at'] = now();
                }
                $ordersToProcess = Order::whereIn('id', $request->order_ids)->get();
                foreach ($ordersToProcess as $order) {
                    $this->deductOrderStock($order);
                    $this->deductOrderBudget($order);
                }
            } elseif (in_array($request->status, ['cancelled', 'rejected'])) {
                $ordersToProcess = Order::whereIn('id', $request->order_ids)->get();
                foreach ($ordersToProcess as $order) {
                    $this->returnOrderStock($order);
                    $this->returnOrderBudget($order);
                }
            }

            $count = Order::whereIn('id', $request->order_ids)->update($data);
        });

        $this->logActivity('Batch Status Updated', "Updated status of {$count} orders to {$request->status}.", ['order_ids' => $request->order_ids, 'new_status' => $request->status]);

        return back()->with('success', "{$count} orders updated to " . ucfirst(str_replace('_', ' ', $request->status)) . ".");
    }

    public function destroy(Order $order)
    {
        DB::transaction(function() use ($order) {
            // Return stock and budget if inventory was deducted
            $this->returnOrderStock($order);
            $this->returnOrderBudget($order);
            
            $this->logActivity('Order Deleted', "Deleted order #{$order->id} for {$order->client_name}.", ['order_id' => $order->id]);
            
            $order->delete();
        });

        return back()->with('success', "Order #{$order->id} has been deleted successfully.");
    }

    public function batchDestroy(Request $request)
    {
        $orderIds = $request->order_ids;
        if (empty($orderIds)) {
            return back()->with('error', 'No orders selected.');
        }

        $count = 0;
        DB::transaction(function() use ($orderIds, &$count) {
            $orders = Order::whereIn('id', $orderIds)->get();
            foreach ($orders as $order) {
                $this->returnOrderStock($order);
                $this->returnOrderBudget($order);
                $order->delete();
                $count++;
            }
        });

        $this->logActivity('Batch Orders Deleted', "Deleted {$count} orders.", ['order_ids' => $orderIds]);

        return back()->with('success', "{$count} orders have been deleted successfully.");
    }
}
