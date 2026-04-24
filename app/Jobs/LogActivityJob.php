<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $action;
    protected $description;
    protected $details;
    protected $ip;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $action, $description, $details, $ip, $userAgent)
    {
        $this->userId = $userId;
        $this->action = $action;
        $this->description = $description;
        $this->details = $details;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ActivityLog::create([
            'user_id' => $this->userId,
            'action' => $this->action,
            'description' => $this->description,
            'details' => $this->details,
            'ip_address' => $this->ip,
            'user_agent' => $this->userAgent,
        ]);
    }
}
