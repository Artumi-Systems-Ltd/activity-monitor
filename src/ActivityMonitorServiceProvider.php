<?php

namespace VaraTech\ActivityMonitor;

use Illuminate\Support\ServiceProvider;
use VaraTech\ActivityMonitor\Listeners\LoginListener;
use VaraTech\ActivityMonitor\Listeners\LogoutListener;
use VaraTech\ActivityMonitor\Listeners\ModelEventListener;
use VaraTech\ActivityMonitor\Middleware\LogRequestActivity;
use VaraTech\ActivityMonitor\Console\Commands\CleanupActivitiesCommand;
use VaraTech\ActivityMonitor\Console\Commands\ShowActivityStatsCommand;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

class ActivityMonitorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/activity-monitor.php',
            'activity-monitor'
        );

        $this->app->singleton('activity-monitor', function ($app) {
            return new ActivityMonitor();
        });
    }

    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/activity-monitor.php' => config_path('activity-monitor.php'),
        ], 'activity-monitor-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'activity-monitor-migrations');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupActivitiesCommand::class,
                ShowActivityStatsCommand::class,
            ]);
        }

        // Register event listeners
        $this->registerEventListeners();

        // Register middleware
        $this->registerMiddleware();

        // Register model observers
        $this->registerModelObservers();
    }

    protected function registerEventListeners()
    {
        Event::listen(Login::class, LoginListener::class);
        Event::listen(Logout::class, LogoutListener::class);
    }

    protected function registerMiddleware()
    {
        if (config('activity-monitor.log_all_requests', false)) {
            $this->app['router']->pushMiddlewareToGroup('web', LogRequestActivity::class);
            $this->app['router']->pushMiddlewareToGroup('api', LogRequestActivity::class);
        }
    }

    protected function registerModelObservers()
    {
        $trackedModels = config('activity-monitor.track_models', []);
        
        foreach ($trackedModels as $model) {
            if (class_exists($model)) {
                $model::observe(ModelEventListener::class);
            }
        }
    }
} 