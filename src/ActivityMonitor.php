<?php

namespace VaraTech\ActivityMonitor;

use VaraTech\ActivityMonitor\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityMonitor
{
    /**
     * Log an activity.
     */
    public function log(string $action, array $properties = [], $subject = null, string $description = null): Activity
    {
        $activity = new Activity();
        
        $activity->fill([
            'action' => $action,
            'description' => $description,
            'properties' => array_merge($this->getDefaultProperties(), $properties),
        ]);

        // Set user information
        if (Auth::check()) {
            $user = Auth::user();
            $activity->user_id = $user->getKey();
            $activity->user_type = get_class($user);
        }

        // Set subject information
        if ($subject) {
            $activity->subject_type = get_class($subject);
            $activity->subject_id = $subject->getKey();
        }

        $activity->save();

        return $activity;
    }

    /**
     * Log a model event.
     */
    public function logModelEvent(string $event, $model, array $properties = []): Activity
    {
        $action = "model.{$event}";
        $description = $this->generateModelEventDescription($event, $model);
        
        $properties = array_merge($properties, [
            'model_class' => get_class($model),
            'model_id' => $model->getKey(),
        ]);

        return $this->log($action, $properties, $model, $description);
    }

    /**
     * Log a request.
     */
    public function logRequest(Request $request, array $properties = []): Activity
    {
        $properties = array_merge($properties, [
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'parameters' => $request->except(['password', 'password_confirmation', '_token']),
        ]);

        return $this->log('request', $properties, null, "HTTP {$request->method()} request to {$request->path()}");
    }

    /**
     * Log an authentication event.
     */
    public function logAuth(string $event, $user = null, array $properties = []): Activity
    {
        $user = $user ?: Auth::user();
        
        $activity = new Activity();
        
        $activity->fill([
            'action' => "auth.{$event}",
            'description' => $this->generateAuthEventDescription($event, $user),
            'properties' => array_merge($this->getDefaultProperties(), $properties),
        ]);

        if ($user) {
            $activity->user_id = $user->getKey();
            $activity->user_type = get_class($user);
        }

        $activity->save();

        return $activity;
    }

    /**
     * Get default properties that should be included with every activity.
     */
    protected function getDefaultProperties(): array
    {
        $properties = [];
        $config = config('activity-monitor.log_properties', []);

        if (app()->runningInConsole()) {
            return $properties;
        }

        $request = request();

        if ($config['ip_address'] ?? false) {
            $properties['ip_address'] = $request->ip();
        }

        if ($config['user_agent'] ?? false) {
            $properties['user_agent'] = $request->userAgent();
        }

        if ($config['url'] ?? false) {
            $properties['url'] = $request->fullUrl();
        }

        if ($config['method'] ?? false) {
            $properties['method'] = $request->method();
        }

        return $properties;
    }

    /**
     * Generate a description for model events.
     */
    protected function generateModelEventDescription(string $event, $model): string
    {
        $modelName = class_basename($model);
        $modelId = $model->getKey();

        return match ($event) {
            'created' => "Created {$modelName} #{$modelId}",
            'updated' => "Updated {$modelName} #{$modelId}",
            'deleted' => "Deleted {$modelName} #{$modelId}",
            default => "Performed {$event} on {$modelName} #{$modelId}",
        };
    }

    /**
     * Generate a description for authentication events.
     */
    protected function generateAuthEventDescription(string $event, $user): string
    {
        $userName = $user ? ($user->name ?? $user->email ?? "User #{$user->getKey()}") : 'Unknown user';

        return match ($event) {
            'login' => "User {$userName} logged in",
            'logout' => "User {$userName} logged out",
            default => "Authentication event: {$event} for {$userName}",
        };
    }
} 