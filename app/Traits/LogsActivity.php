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
        return ActivityLog::create([
            'user_id' => auth('web')->id(),
            'action' => $action,
            'description' => $description,
            'details' => $details,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }
}
