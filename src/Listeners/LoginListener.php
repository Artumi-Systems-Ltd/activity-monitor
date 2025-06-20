<?php

namespace VaraTech\ActivityMonitor\Listeners;

use Illuminate\Auth\Events\Login;
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

class LoginListener
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (!config('activity-monitor.log_authentication_events.login', true)) {
            return;
        }

        ActivityMonitor::logAuth('login', $event->user, [
            'guard' => $event->guard,
            'remember' => $event->remember,
        ]);
    }
} 