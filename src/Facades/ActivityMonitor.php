<?php

namespace VaraTech\ActivityMonitor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \VaraTech\ActivityMonitor\Models\Activity log(string $action, array $properties = [], $subject = null, string $description = null)
 * @method static \VaraTech\ActivityMonitor\Models\Activity logModelEvent(string $event, $model, array $properties = [])
 * @method static \VaraTech\ActivityMonitor\Models\Activity logRequest(\Illuminate\Http\Request $request, array $properties = [])
 * @method static \VaraTech\ActivityMonitor\Models\Activity logAuth(string $event, $user = null, array $properties = [])
 */
class ActivityMonitor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'activity-monitor';
    }
} 