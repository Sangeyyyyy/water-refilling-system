<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log a system activity.
     *
     * @param string $action The high-level action (e.g., 'Inventory Updated')
     * @param string $description Detailed description of what happened
     * @param array|null $details Raw data for auditing
     * @return ActivityLog
     */
    protected function logActivity($action, $description, $details = null)
    {
        // Capture context request data before dispatching the job
        $userId = auth('web')->id() ?? auth('client')->id();
        $ip = Request::ip();
        $userAgent = Request::header('User-Agent');

        \App\Jobs\LogActivityJob::dispatch($userId, $action, $description, $details, $ip, $userAgent);
    }
}
