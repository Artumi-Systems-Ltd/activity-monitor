<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Request Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable automatic logging of all HTTP requests.
    | When enabled, all requests will be logged with basic information.
    |
    */
    'log_all_requests' => env('ACTIVITY_LOG_ALL_REQUESTS', false),

    /*
    |--------------------------------------------------------------------------
    | Tracked Models
    |--------------------------------------------------------------------------
    |
    | Define which Eloquent models should be automatically tracked for
    | create, update, and delete events. Add the full class name.
    |
    */
    'track_models' => [
        // App\Models\Post::class,
        // App\Models\Order::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    |
    | The database table name where activities will be stored.
    |
    */
    'database_table' => 'activities',

    /*
    |--------------------------------------------------------------------------
    | Authentication Events
    |--------------------------------------------------------------------------
    |
    | Enable or disable logging of authentication events.
    |
    */
    'log_authentication_events' => [
        'login' => true,
        'logout' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    |
    | Define which model events should be tracked.
    |
    */
    'log_model_events' => [
        'created' => true,
        'updated' => true,
        'deleted' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Properties to Log
    |--------------------------------------------------------------------------
    |
    | Define what additional properties should be logged with each activity.
    |
    */
    'log_properties' => [
        'ip_address' => true,
        'user_agent' => true,
        'url' => true,
        'method' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Settings
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of old activity logs.
    | Set to null to disable automatic cleanup.
    |
    */
    'cleanup' => [
        'enabled' => false,
        'older_than_days' => 90,
    ],
]; 