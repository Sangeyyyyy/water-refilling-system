<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin'])) {
            abort(403);
        }

        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('export')) {
            return $this->export($query->orderBy('created_at', 'desc')->get());
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();
        
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('admin.logs', compact('logs', 'actions'));
    }

    private function export($logs)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=activity_logs_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date', 'User', 'Action', 'Description', 'IP Address'];

        $callback = function() use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            $sanitize = function($value) {
                if (is_string($value) && preg_match('/^[=\+\-@]/', $value)) {
                    return "'" . $value;
                }
                return $value;
            };

            foreach ($logs as $log) {
                $row = [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $sanitize($log->user ? $log->user->name : 'System'),
                    $sanitize($log->action),
                    $sanitize($log->description),
                    $sanitize($log->ip_address ?? 'N/A')
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
