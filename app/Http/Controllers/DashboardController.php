<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Office;
use App\Models\User;
use App\Models\Ppmp;
use App\Models\Campus;
use App\Models\Inventory;
use App\Models\Setting;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,client');
    }

    public function index(Request $request)
    {
        $clientUser = auth('client')->user();
        if ($clientUser) {
            return $this->getClientDashboard($clientUser, $request);
        }

        return $this->getAdminDashboard(auth()->user(), $request);
    }

    private function getClientDashboard($clientUser, Request $request)
    {
        $baseQuery = Order::with(['office.division.campus', 'client'])
            ->where('client_id', $clientUser->id);

        // Active Orders: Confirmed, Out for Delivery
        $activeOrders = (clone $baseQuery)
            ->whereIn('status', ['confirmed', 'out_for_delivery'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // History Orders: Completed, Cancelled, Rejected
        $historyQuery = (clone $baseQuery)
            ->whereIn('status', ['completed', 'cancelled', 'rejected']);

        if ($request->filled('search')) {
             $search = $request->search;
             $historyQuery->where(function($q) use ($search) {
                 $q->where('id', 'like', "%{$search}%")
                   ->orWhere('pr_number', 'like', "%{$search}%");
             });
        }

        if ($request->filled('date_from')) {
            $historyQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $historyQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $historyQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        $stats = [
            'pending' => 0,
            'confirmed' => $activeOrders->where('status', 'confirmed')->count(),
            'out_for_delivery' => $activeOrders->where('status', 'out_for_delivery')->count(),
            'today_orders' => (clone $baseQuery)->whereDate('created_at', Carbon::today())->count(),
        ];
        
        $offices = \Illuminate\Support\Facades\Cache::remember('offices_for_order', 3600, fn() =>
            Office::with('division.campus')->orderBy('name')->get()
        );
        $unitPrice = Setting::getUnitPrice();
        
        return view('home', compact('orders', 'activeOrders', 'stats', 'offices', 'unitPrice'));
    }

    private function getAdminDashboard($user, Request $request)
    {
        $query = Order::with(['office.division.campus', 'client']);
        
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if ($user->role !== 'staff') {
            if (!$dateFrom && !$dateTo) {
                $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
                $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
            }

            if ($dateFrom) {
                $query->whereDate('orders.delivery_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('orders.delivery_date', '<=', $dateTo);
            }
        }
        
        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }
        if ($request->filled('customer_type')) {
            $query->where('orders.customer_type', $request->customer_type);
        }
        if ($request->filled('order_type')) {
            $query->where('orders.order_type', $request->order_type);
        }
        
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        $stats = $this->getStats($user, $query);

        $inventoryStats = Inventory::all()->keyBy('item_name');
        
        if (!isset($stats['month_sales'])) $stats['month_sales'] = 0;
        if (!isset($stats['month_completed'])) $stats['month_completed'] = 0;
        if (!isset($stats['month_gallons'])) $stats['month_gallons'] = 0;
        $stats['inventory'] = $inventoryStats;

        $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $pendingOrders = $emptyPaginator;
        $deliveryQueue = $emptyPaginator;
        $completedToday = $emptyPaginator;
        $sort = $request->get('sort', 'desc');

        if (in_array($user->role, config('roles.admin_roles'))) {
            $pendingOrders = $this->getPendingQueue($query, $dateFrom, $dateTo, $request);
            $deliveryQueue = $this->getDeliveryQueue($dateTo, $request);
            $completedToday = $this->getCompletedToday();
            $orders = $query->orderBy('reference_number', 'asc')->paginate(10)->withQueryString();
        } else {
            $orders = $query->orderBy('created_at', $sort)->paginate(10)->withQueryString();
        }
        
        $offices = \Illuminate\Support\Facades\Cache::remember('offices_for_order', 3600, fn() =>
            Office::with('division.campus')->orderBy('name')->get()
        );

        $dashboardData = [];
        if (in_array($user->role, config('roles.management_roles'))) {
            $dashboardData = $this->getAdvancedDashboardData($user, $inventoryStats);

            $stats['month_completed'] = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'completed')
                ->count();

            $stats['month_gallons'] = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->sum('quantity');

            $stats['month_orders'] = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->count();
        }

        $unitPrice = Setting::getUnitPrice();
        
        $roleViews = [
            'admin' => 'admin.dashboards.admin',
            'director' => 'admin.dashboards.director',
            'manager' => 'admin.dashboards.manager',
            'staff' => 'admin.dashboards.staff'
        ];
        
        if (!array_key_exists($user->role, $roleViews)) {
            abort(403, 'Unauthorized dashboard access.');
        }

        $view = $roleViews[$user->role];

        return view($view, compact('dateFrom', 'dateTo', 'orders', 'stats', 'offices', 'sort', 'dashboardData', 'unitPrice', 'pendingOrders', 'deliveryQueue', 'completedToday'));
    }

    private function getStats($user, $query)
    {
        $cacheKey = 'dashboard_stats_' . $user->id . '_' . md5(serialize(request()->all()));
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($user, $query) {
            $statsQuery = clone $query;
            
            $rawStats = $statsQuery->selectRaw("
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status IN ('confirmed', 'out_for_delivery') THEN 1 END) as confirmed,
                COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) as today_orders
            ")->first();

            $stats = [
                'pending' => $rawStats->pending ?? 0,
                'confirmed' => $rawStats->confirmed ?? 0,
                'today_orders' => $rawStats->today_orders ?? 0,
            ];

            if ($user->role === 'staff') {
                $staffStats = Order::selectRaw("
                    COUNT(CASE WHEN status = 'out_for_delivery' THEN 1 END) as out_for_delivery,
                    COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_only,
                    COUNT(CASE WHEN status = 'completed' AND DATE(updated_at) = CURRENT_DATE THEN 1 END) as completed_today
                ")->first();

                $stats['out_for_delivery'] = $staffStats->out_for_delivery ?? 0;
                $stats['confirmed_only'] = $staffStats->confirmed_only ?? 0;
                $stats['completed_today'] = $staffStats->completed_today ?? 0;
            }

            if (in_array($user->role, config('roles.admin_roles'))) {
                $adminStats = Order::selectRaw("
                    SUM(CASE WHEN DATE(created_at) = CURRENT_DATE AND status IN ('confirmed', 'completed', 'out_for_delivery') THEN total_amount ELSE 0 END) as today_sales,
                    SUM(CASE WHEN DATE(created_at) = CURRENT_DATE AND status IN ('confirmed', 'completed', 'out_for_delivery') THEN quantity ELSE 0 END) as today_gallons,
                    SUM(CASE WHEN MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE) AND status IN ('confirmed', 'completed', 'out_for_delivery') THEN total_amount ELSE 0 END) as month_sales,
                    COUNT(CASE WHEN customer_type = 'Office' THEN 1 END) as office_orders,
                    COUNT(CASE WHEN customer_type = 'Individual' THEN 1 END) as individual_orders
                ")->first();

                $stats['today_sales'] = $adminStats->today_sales ?? 0;
                $stats['today_gallons'] = $adminStats->today_gallons ?? 0;
                $stats['month_sales'] = $adminStats->month_sales ?? 0;
                $stats['office_orders'] = $adminStats->office_orders ?? 0;
                $stats['individual_orders'] = $adminStats->individual_orders ?? 0;
            }

            return $stats;
        });
    }

    private function getPendingQueue($query, $dateFrom, $dateTo, Request $request)
    {
        $pendingOrdersQuery = (clone $query)->where('orders.status', 'confirmed');
        
        if ($dateFrom) {
            $pendingOrdersQuery = Order::with(['office.division.campus', 'client'])
                ->where('status', 'confirmed')
                ->where(function($q) use ($dateFrom) {
                    $q->whereDate('delivery_date', '>=', $dateFrom)
                      ->orWhereDate('delivery_date', '<', $dateFrom);
                });
            
            if ($dateTo) {
                $pendingOrdersQuery->whereDate('delivery_date', '<=', $dateTo);
            }
            
            if ($request->filled('search')) {
                $pendingOrdersQuery->search($request->search);
            }
        }

        return $this->applyQueueJoinsAndOrdering($pendingOrdersQuery)
            ->paginate(50, ['*'], 'pending_page');
    }

    private function getDeliveryQueue($dateTo, Request $request)
    {
        $deliveryQueueQuery = Order::with(['office.division.campus', 'client'])
            ->where('status', 'out_for_delivery');
        
        if ($dateTo) {
            $deliveryQueueQuery->whereDate('delivery_date', '<=', $dateTo);
        }
        
        if ($request->filled('search')) {
            $deliveryQueueQuery->search($request->search);
        }
        
        return $this->applyQueueJoinsAndOrdering($deliveryQueueQuery)
            ->paginate(20, ['*'], 'delivery_page');
    }

    private function applyQueueJoinsAndOrdering($query)
    {
        return $query
            ->leftJoin('offices', 'orders.office_id', '=', 'offices.id')
            ->leftJoin('divisions', 'offices.division_id', '=', 'divisions.id')
            ->leftJoin('campuses', 'divisions.campus_id', '=', 'campuses.id')
            ->select('orders.*', 'campuses.name as campus_name', 'divisions.name as division_name', 'offices.name as office_name')
            ->orderBy('orders.delivery_date', 'asc')
            ->orderBy('campus_name', 'asc')
            ->orderBy('division_name', 'asc')
            ->orderBy('office_name', 'asc')
            ->orderBy('orders.created_at', 'asc');
    }

    private function getCompletedToday()
    {
        return Order::with('office.division.campus')
            ->where('orders.status', 'completed')
            ->whereDate('orders.updated_at', Carbon::today())
            ->orderBy('orders.updated_at', 'desc')
            ->paginate(10, ['*'], 'completed_page');
    }

    private function getAdvancedDashboardData($user, $inventoryStats)
    {
        $cacheKey = 'advanced_dashboard_data_' . $user->id;
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function() use ($user, $inventoryStats) {
            $dashboardData = [];
            
            $salesTrend = Order::where('created_at', '>=', Carbon::now()->subDays(30))
                ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
            
            $dashboardData['sales_trend'] = [
                'labels' => $salesTrend->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d')),
                'data' => $salesTrend->pluck('total')
            ];

            $campusConsumption = DB::table('orders')
                ->join('offices', 'orders.office_id', '=', 'offices.id')
                ->join('divisions', 'offices.division_id', '=', 'divisions.id')
                ->join('campuses', 'divisions.campus_id', '=', 'campuses.id')
                ->whereIn('orders.status', ['confirmed', 'completed', 'out_for_delivery'])
                ->select('campuses.name', DB::raw('SUM(orders.quantity) as volume'))
                ->groupBy('campuses.name')
                ->get();
            
            $dashboardData['campus_consumption'] = [
                'labels' => $campusConsumption->pluck('name'),
                'data' => $campusConsumption->pluck('volume')
            ];

            if (in_array($user->role, config('roles.management_roles'))) {
                $dashboardData['user_counts'] = [
                    'total' => User::count(),
                    'clients' => Client::count(),
                    'staff' => User::whereIn('role', config('roles.admin_roles'))->count()
                ];
            }

            $dashboardData['low_ppmp_alerts'] = Ppmp::with('office')
                ->where('status', 'approved')
                ->whereRaw('remaining_budget <= (total_budget * 0.2)')
                ->get();
            
            $dashboardData['inventory_alerts'] = Inventory::whereRaw('stock_level <= low_stock_threshold')->get();

            $dashboardData['top_units'] = Order::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereIn('status', ['confirmed', 'completed', 'out_for_delivery'])
                ->whereNotNull('office_id')
                ->select('office_id', DB::raw('SUM(quantity) as total_gallons'))
                ->with('office')
                ->groupBy('office_id')
                ->orderByDesc('total_gallons')
                ->take(5)
                ->get();

            return $dashboardData;
        });
    }
}
