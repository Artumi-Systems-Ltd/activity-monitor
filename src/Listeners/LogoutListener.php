<?php

namespace VaraTech\ActivityMonitor\Listeners;

use Illuminate\Auth\Events\Logout;
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

class LogoutListener
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if (!config('activity-monitor.log_authentication_events.logout', true)) {
            return;
        }

        ActivityMonitor::logAuth('logout', $event->user, [
            'guard' => $event->guard,
        ]);
    }
} 