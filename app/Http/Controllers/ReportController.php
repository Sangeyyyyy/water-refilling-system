<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,client');
    }

    public function billing(Order $order)
    {
        return view('reports.billing', compact('order'));
    }

    public function deliveryReceipt(Order $order)
    {
        return view('reports.delivery_receipt', compact('order'));
    }

    public function hub(Request $request)
    {
        $financialData = $this->getFinancialData($request);
        $operationalData = $this->getOperationalData($request);
        $inventoryData = $this->getInventoryData();
        $adminStats = $this->getAdminStats();

        $startDate = $financialData['startDate'];
        $endDate = $financialData['endDate'];

        return view('reports.hub', array_merge(
            $financialData, 
            $operationalData, 
            $inventoryData, 
            $adminStats,
            ['startDate' => $startDate, 'endDate' => $endDate]
        ));
    }

    public function batchDeliveryReceipts(\Illuminate\Http\Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $orders = \App\Models\Order::with('office')->whereIn('id', $ids)->get();
 
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No orders selected for printing.');
        }
 
        // Group orders for consolidation
        // Criteria: Same Date, Same Client, Same Office/Other Location, Same Delivery Window
        $groupedOrders = $orders->groupBy(function($order) {
            return ($order->delivery_date ? $order->delivery_date->toDateString() : 'no-date') . '-' .
                   $order->full_client_name . '-' .
                   ($order->office_id ?? 'custom-') . ($order->other_location ?? '');
        })->map(function($group) {
            // Take the first order as the "primary" for header info
            $primary = $group->first();
            
            // Consolidate reference numbers
            $references = $group->pluck('reference_number')->filter()->unique()->implode(', ');
            if (empty($references)) {
                $references = $group->map(fn($o) => '#' . str_pad($o->id, 5, '0', STR_PAD_LEFT))->implode(', ');
            }
 
            return (object) [
                'primary' => $primary,
                'items' => $group, // List all orders in this group as items
                'references' => $references,
                'total_amount' => $group->sum('total_amount'),
                'delivery_date' => $primary->delivery_date,
                'full_client_name' => $primary->full_client_name,
                'location' => $primary->office->name ?? $primary->other_location,
                'pr_numbers' => $group->pluck('pr_number')->filter()->unique()->implode(', ') ?: 'N/A'
            ];
        });
 
        return view('reports.batch_delivery_receipts', compact('groupedOrders'));
    }
 
    public function batchBilling(\Illuminate\Http\Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $orders = \App\Models\Order::with('office')->whereIn('id', $ids)->get();
 
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'No orders selected for printing.');
        }
 
        return view('reports.batch_billing', compact('orders'));
    }

    public function printFinancial(Request $request)
    {
        $data = $this->getFinancialData($request);
        return view('reports.print.financial', $data);
    }

    public function printOperational(Request $request)
    {
        $data = $this->getOperationalData($request);
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::now()->endOfMonth();
        return view('reports.print.operational', array_merge($data, ['startDate' => $startDate, 'endDate' => $endDate]));
    }

    public function printInventory()
    {
        $data = $this->getInventoryData();
        return view('reports.print.inventory', $data);
    }


    private function getFinancialData(Request $request)
    {
        $query = Order::with(['office.division.campus', 'client'])->latest();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        } else {
            // Default to this month
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        $totalSales = $orders->sum('total_amount');

        // Chart 1: Revenue Trend (Day-by-day)
        $revenueTrend = $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'))
            ->map(fn($group) => $group->sum('total_amount'))
            ->sortKeys();

        // Summary facts
        $summary = [
            'refills' => (clone $orders)->where('is_refill', true)->sum('quantity'),
            'new_gallons' => (clone $orders)->where('is_refill', false)->sum('quantity'),
            'individual' => (clone $orders)->where('customer_type', 'Individual')->count(),
            'office' => (clone $orders)->where('customer_type', 'Office')->count(),
            'average_order' => $orders->count() > 0 ? $totalSales / $orders->count() : 0,
        ];

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        // Previous period for comparison
        $diffInDays = $startDate->diffInDays($endDate) + 1;
        $prevStart = (clone $startDate)->subDays($diffInDays);
        $prevEnd = (clone $startDate)->subSecond();

        $prevOrders = Order::whereBetween('created_at', [$prevStart, $prevEnd]);
        if ($request->status) { $prevOrders->where('status', $request->status); }
        $prevOrders = $prevOrders->get();
        $prevTotalSales = $prevOrders->sum('total_amount');

        $trends = [
            'revenue' => $this->calculateTrend($totalSales, $prevTotalSales),
            'orders' => $this->calculateTrend($orders->count(), $prevOrders->count()),
            'volume' => $this->calculateTrend($orders->sum('quantity'), $prevOrders->sum('quantity')),
            'period_label' => $diffInDays <= 1 ? 'vs yesterday' : "vs prev $diffInDays days"
        ];

        $financialChartData = [
            'revenue_trend' => [
                'labels' => $revenueTrend->keys()->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray(),
                'data' => $revenueTrend->values()->toArray()
            ],
            'segment_split' => [
                'labels' => ['Office', 'Individual'],
                'data' => [$summary['office'], $summary['individual']]
            ],
            'source_split' => [
                'labels' => ['Refill', 'New Gallon'],
                'data' => [$summary['refills'], $summary['new_gallons']]
            ]
        ];

        return [
            'orders' => $orders,
            'totalSales' => $totalSales,
            'summary' => $summary,
            'financialChartData' => $financialChartData,
            'financialTrends' => $trends,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

    private function getOperationalData(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfMonth();

        $query = Order::whereIn('status', ['confirmed', 'completed', 'out_for_delivery']);
        $query->whereBetween('delivery_date', [$startDate, $endDate]);

        $orders = $query->get();

        // Consumption by Campus
        $campusConsumption = \Illuminate\Support\Facades\DB::table('orders')
            ->join('offices', 'orders.office_id', '=', 'offices.id')
            ->join('divisions', 'offices.division_id', '=', 'divisions.id')
            ->join('campuses', 'divisions.campus_id', '=', 'campuses.id')
            ->whereIn('orders.status', ['confirmed', 'completed', 'out_for_delivery'])
            ->whereBetween('orders.delivery_date', [$startDate, $endDate])
            ->select('campuses.name', \Illuminate\Support\Facades\DB::raw('SUM(orders.quantity) as volume'))
            ->groupBy('campuses.name')
            ->get();

        // Top consuming offices
        $topOffices = \Illuminate\Support\Facades\DB::table('orders')
            ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
            ->whereBetween('delivery_date', [$startDate, $endDate])
            ->join('offices', 'orders.office_id', '=', 'offices.id')
            ->select('offices.name as office_name', \Illuminate\Support\Facades\DB::raw('SUM(orders.quantity) as volume'))
            ->groupBy('offices.name', 'orders.office_id')
            ->orderByDesc('volume')
            ->take(10)
            ->get();

        // Previous period comparison
        $diffInDays = $startDate->diffInDays($endDate) + 1;
        $prevStart = (clone $startDate)->subDays($diffInDays);
        $prevEnd = (clone $startDate)->subSecond();
        
        $prevOrders = Order::whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
            ->whereBetween('delivery_date', [$prevStart, $prevEnd])
            ->get();

        $operationalTrends = [
            'volume' => $this->calculateTrend($orders->sum('quantity'), $prevOrders->sum('quantity')),
            'deliveries' => $this->calculateTrend($orders->count(), $prevOrders->count()),
            'active_units' => $this->calculateTrend(
                $orders->whereNotNull('office_id')->pluck('office_id')->unique()->count(),
                $prevOrders->whereNotNull('office_id')->pluck('office_id')->unique()->count()
            ),
            'period_label' => $diffInDays <= 1 ? 'vs yesterday' : "vs prev $diffInDays days"
        ];

        $operationalChartData = [
            'campus_consumption' => [
                'labels' => $campusConsumption->pluck('name'),
                'data' => $campusConsumption->pluck('volume')
            ],
            'top_units' => [
                'labels' => $topOffices->pluck('office_name'),
                'data' => $topOffices->pluck('volume')
            ]
        ];

        return [
            'campusConsumption' => $campusConsumption,
            'topOffices' => $topOffices,
            'operationalOrders' => $orders,
            'operationalChartData' => $operationalChartData,
            'operationalTrends' => $operationalTrends
        ];
    }

    private function getInventoryData()
    {
        $items = \App\Models\Inventory::all();
        
        $office_distribution = \App\Models\Office::with('division.collegeOffice.campus')
            ->where('gallon_count', '>', 0)
            ->orderBy('gallon_count', 'desc')
            ->get();
            
        $user_distribution = \App\Models\User::where('gallon_count', '>', 0)
            ->orderBy('gallon_count', 'desc')
            ->get();
            
        $client_distribution = \App\Models\Client::where('gallon_count', '>', 0)
            ->orderBy('gallon_count', 'desc')
            ->get();

        $total_in_circulation = $office_distribution->sum('gallon_count') + 
                                $user_distribution->sum('gallon_count') + 
                                $client_distribution->sum('gallon_count');

        $stockData = [
            'labels' => $items->pluck('item_name'),
            'current' => $items->pluck('stock_level'),
            'threshold' => $items->pluck('low_stock_threshold')
        ];

        $circulationData = [
            'labels' => ['Offices', 'Staff', 'Clients'],
            'data' => [$office_distribution->sum('gallon_count'), $user_distribution->sum('gallon_count'), $client_distribution->sum('gallon_count')]
        ];

        return [
            'items' => $items, 
            'office_distribution' => $office_distribution, 
            'user_distribution' => $user_distribution, 
            'client_distribution' => $client_distribution,
            'total_in_circulation' => $total_in_circulation,
            'stockData' => $stockData,
            'circulationData' => $circulationData
        ];
    }

    private function getAdminStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = [
            'total_orders' => Order::count(),
            'month_revenue' => Order::whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('total_amount'),
            'total_users' => \App\Models\User::count(),
            'total_clients' => \App\Models\Client::count(),
        ];

        // Comparison for Overview (This month vs last month)
        $prevStart = (clone $startOfMonth)->subMonth();
        $prevEnd = (clone $endOfMonth)->subMonth();
        $prevRevenue = Order::whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('total_amount');
        
        $overviewTrends = [
            'revenue' => $this->calculateTrend($stats['month_revenue'], $prevRevenue),
            'period_label' => 'vs last month'
        ];

        $statusDistribution = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $orderTrend = Order::where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $adminChartData = [
            'status_distribution' => [
                'labels' => $statusDistribution->pluck('status')->map(fn($s) => ucfirst(str_replace('_', ' ', $s))),
                'data' => $statusDistribution->pluck('count')
            ],
            'order_trend' => [
                'labels' => $orderTrend->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d')),
                'data' => $orderTrend->pluck('count')
            ]
        ];

        return [
            'stats' => $stats,
            'adminChartData' => $adminChartData,
            'overviewTrends' => $overviewTrends
        ];
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function export(Request $request)
    {
        $query = Order::with(['office.division.campus']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        $filename = "orders_export_" . date('Y-m-d_H-i-s') . ".csv";
        if ($request->start_date && $request->end_date) {
            $filename = "orders_" . Carbon::parse($request->start_date)->format('Y-m-d') . "_to_" . Carbon::parse($request->end_date)->format('Y-m-d') . ".csv";
        }

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order ID', 'Date', 'Client', 'Type', 'Location', 'Items', 'Amount', 'Status', 'Payment'];

        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $sanitize = function($value) {
                if (is_string($value) && preg_match('/^[=\+\-@]/', $value)) {
                    return "'" . $value;
                }
                return $value;
            };

            foreach ($orders as $order) {
                // Format location
                $location = $order->office ? 
                    ($order->office->division->campus->name ?? '') . ' - ' . ($order->office->division->name ?? '') . ' - ' . $order->office->name 
                    : $order->other_location;

                $row = [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $sanitize($order->client_name),
                    $sanitize(ucfirst($order->customer_type) . ' (' . ucfirst($order->order_type) . ')'),
                    $sanitize($location),
                    $order->quantity . ' gal',
                    $order->total_amount,
                    $sanitize(ucfirst($order->status)),
                    $sanitize($order->payment_status ?? 'Unpaid')
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
