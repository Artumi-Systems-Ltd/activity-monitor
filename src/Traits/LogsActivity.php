<?php

namespace VaraTech\ActivityMonitor\Traits;

use VaraTech\ActivityMonitor\Models\Activity;
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            if (config('activity-monitor.log_model_events.created', true)) {
                ActivityMonitor::logModelEvent('created', $model);
            }
        });

        static::updated(function ($model) {
            if (config('activity-monitor.log_model_events.updated', true)) {
                ActivityMonitor::logModelEvent('updated', $model);
            }
        });

        static::deleted(function ($model) {
            if (config('activity-monitor.log_model_events.deleted', true)) {
                ActivityMonitor::logModelEvent('deleted', $model);
            }
        });
    }

    /**
     * Get all activities for this model.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    /**
     * Log a custom activity for this model.
     */
    public function logActivity(string $action, array $properties = [], string $description = null): Activity
    {
        return ActivityMonitor::log($action, $properties, $this, $description);
    }
} 