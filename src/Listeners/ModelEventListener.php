<?php

namespace VaraTech\ActivityMonitor\Listeners;

use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

class ModelEventListener
{
    /**
     * Handle the model created event.
     */
    public function created($model): void
    {
        if (!config('activity-monitor.log_model_events.created', true)) {
            return;
        }

        ActivityMonitor::logModelEvent('created', $model, [
            'attributes' => $model->getAttributes(),
        ]);
    }

    /**
     * Handle the model updated event.
     */
    public function updated($model): void
    {
        if (!config('activity-monitor.log_model_events.updated', true)) {
            return;
        }

        ActivityMonitor::logModelEvent('updated', $model, [
            'old_attributes' => $model->getOriginal(),
            'new_attributes' => $model->getAttributes(),
            'changes' => $model->getChanges(),
        ]);
    }

    /**
     * Handle the model deleted event.
     */
    public function deleted($model): void
    {
        if (!config('activity-monitor.log_model_events.deleted', true)) {
            return;
        }

        ActivityMonitor::logModelEvent('deleted', $model, [
            'attributes' => $model->getAttributes(),
        ]);
    }
} 