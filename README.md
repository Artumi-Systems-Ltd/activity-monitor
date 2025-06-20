# 📦 **Laravel User Activity Monitor**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/varatech/activity-monitor.svg?style=flat-square)](https://packagist.org/packages/varatech/activity-monitor)
[![Total Downloads](https://img.shields.io/packagist/dt/varatech/activity-monitor.svg?style=flat-square)](https://packagist.org/packages/varatech/activity-monitor)
[![License](https://img.shields.io/packagist/l/varatech/activity-monitor.svg?style=flat-square)](https://packagist.org/packages/varatech/activity-monitor)

> A lightweight, configurable Laravel package to **log and monitor user activities** in your application. Capture login, logout, CRUD actions, API requests, and more — complete with IP address, user agent, and custom properties.

---

## ✨ **Features**

✅ **Automatic Activity Logging**: Automatically logs user actions (login, logout, create, update, delete)  
✅ **Model Event Tracking**: Tracks model events for specified models  
✅ **Rich Metadata**: Records user ID, IP address, user agent, URL, timestamps  
✅ **Easy Querying**: Easy-to-query activity logs (`Activity::forUser($id)->recent()`)  
✅ **Highly Configurable**: Enable/disable features via config  
✅ **Extendable**: Hook into activity logging for custom actions  
✅ **Clean Schema**: Clean database schema with optional JSON properties  
✅ **Console Commands**: Built-in commands for cleanup and statistics  
✅ **Traits Available**: Use `LogsActivity` trait for automatic model tracking  
✅ **Laravel 9-12 Compatible**: Full support for Laravel 9.x, 10.x, 11.x, and 12.x  
✅ **PHP 8.1-8.3 Ready**: Compatible with modern PHP versions

---

## ⚡ **Installation**

Install the package via Composer:

```bash
composer require varatech/activity-monitor
```

Publish the configuration and migration files:

```bash
php artisan vendor:publish --tag="activity-monitor-config"
php artisan vendor:publish --tag="activity-monitor-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

---

## 🔧 **Configuration**

The package comes with a comprehensive configuration file. Here are the key options:

```php
// config/activity-monitor.php
return [
    // Enable automatic request logging
    'log_all_requests' => env('ACTIVITY_LOG_ALL_REQUESTS', false),

    // Models to automatically track
    'track_models' => [
        App\Models\Post::class,
        App\Models\Order::class,
    ],

    // Authentication event logging
    'log_authentication_events' => [
        'login' => true,
        'logout' => true,
    ],

    // Model events to track
    'log_model_events' => [
        'created' => true,
        'updated' => true,
        'deleted' => true,
    ],

    // Properties to include with each log
    'log_properties' => [
        'ip_address' => true,
        'user_agent' => true,
        'url' => true,
        'method' => true,
    ],

    // Automatic cleanup settings
    'cleanup' => [
        'enabled' => false,
        'older_than_days' => 90,
    ],
];
```

---

## 🚀 **Usage**

### Manual Activity Logging

```php
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;

// Basic activity logging
ActivityMonitor::log('custom_action', [
    'note' => 'Something important happened',
    'extra_data' => 'Additional context',
]);

// Log with subject (related model)
$post = Post::find(1);
ActivityMonitor::log('post_viewed', ['ip' => request()->ip()], $post, 'User viewed blog post');

// Log authentication events
ActivityMonitor::logAuth('login', auth()->user(), ['guard' => 'web']);

// Log HTTP requests
ActivityMonitor::logRequest(request(), ['custom_property' => 'value']);
```

### Using the LogsActivity Trait

Add the trait to your models for automatic activity logging:

```php
use VaraTech\ActivityMonitor\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
    
    // Now all created, updated, deleted events are automatically logged
}

// Manual logging on the model
$post = Post::find(1);
$post->logActivity('published', ['published_at' => now()], 'Post was published');

// Get all activities for a model
$activities = $post->activities;
```

### Querying Activities

The `Activity` model comes with helpful query scopes:

```php
use VaraTech\ActivityMonitor\Models\Activity;

// Get recent activities for a user
$recent = Activity::forUser(auth()->id())->recent(20)->get();

// Get activities by action
$logins = Activity::byAction('auth.login')->get();

// Get today's activities
$today = Activity::today()->get();

// Get activities for a specific model
$postActivities = Activity::forSubject($post)->get();

// Get activities within date range
$activities = Activity::betweenDates(
    Carbon::now()->subWeek(),
    Carbon::now()
)->get();

// Complex queries
$activities = Activity::forUser(auth()->id())
    ->byAction('model.updated')
    ->where('subject_type', Post::class)
    ->recent(50)
    ->get();
```

### Working with Activity Properties

```php
$activity = Activity::first();

// Get a property value
$ipAddress = $activity->getProperty('ip_address');
$customData = $activity->getProperty('custom_data', 'default_value');

// Check if property exists
if ($activity->hasProperty('user_agent')) {
    echo $activity->getProperty('user_agent');
}

// Access properties directly (JSON field)
$allProperties = $activity->properties;
```

---

## 🛠 **Advanced Usage**

### Automatic Model Tracking

Configure models to be automatically tracked in your config:

```php
// config/activity-monitor.php
'track_models' => [
    App\Models\User::class,
    App\Models\Post::class,
    App\Models\Order::class,
],
```

### Request Logging

Enable automatic request logging:

```php
// .env
ACTIVITY_LOG_ALL_REQUESTS=true
```

Or in config:

```php
'log_all_requests' => true,
```

### Console Commands

#### View Activity Statistics

```bash
# Show stats for the last 30 days
php artisan activity-monitor:stats

# Show stats for the last 7 days
php artisan activity-monitor:stats --days=7

# Show stats for a specific user
php artisan activity-monitor:stats --user=123
```

#### Clean Up Old Activities

```bash
# Clean up activities older than configured days
php artisan activity-monitor:cleanup

# Clean up activities older than 30 days
php artisan activity-monitor:cleanup --days=30

# Dry run (see what would be deleted)
php artisan activity-monitor:cleanup --dry-run
```

---

## 📊 **Database Schema**

The activities table includes these fields:

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `action` | string | The action performed |
| `description` | string | Human-readable description |
| `user_id` | bigint | ID of the user who performed the action |
| `user_type` | string | Class name of the user model |
| `subject_type` | string | Class name of the subject model |
| `subject_id` | bigint | ID of the subject model |
| `properties` | json | Additional properties and metadata |
| `ip_address` | string | User's IP address |
| `user_agent` | text | User's browser/client info |
| `url` | string | Request URL |
| `method` | string | HTTP method |
| `created_at` | timestamp | When the activity occurred |
| `updated_at` | timestamp | Last updated |

---

## 🎯 **Example Use Cases**

### Audit Trails for Admin Actions

```php
// In your admin controller
public function deleteUser(User $user)
{
    ActivityMonitor::log('admin.user_deleted', [
        'deleted_user_id' => $user->id,
        'deleted_user_email' => $user->email,
        'reason' => request('reason'),
    ], $user, "Admin deleted user: {$user->email}");
    
    $user->delete();
}
```

### Security Monitoring

```php
// Track suspicious login attempts
Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
    ActivityMonitor::log('auth.failed', [
        'email' => $event->credentials['email'] ?? null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ], null, 'Failed login attempt');
});
```

### API Usage Analytics

```php
// In your API middleware
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    if ($request->is('api/*')) {
        ActivityMonitor::logRequest($request, [
            'api_version' => $request->header('API-Version'),
            'response_time' => microtime(true) - LARAVEL_START,
            'response_status' => $response->getStatusCode(),
        ]);
    }
    
    return $response;
}
```

### User Behavior Tracking

```php
// Track user interactions
class PostController extends Controller
{
    public function show(Post $post)
    {
        $post->logActivity('viewed', [
            'referrer' => request()->header('referer'),
            'read_time_estimate' => strlen(strip_tags($post->content)) / 200, // words per minute
        ], 'User viewed blog post');
        
        return view('posts.show', compact('post'));
    }
}
```

---

## 🔒 **Security**

If you discover any security-related issues, please email constantinomsigwa@gmail.com instead of using the issue tracker.

---

## 🧪 **Testing**

```bash
composer test
```

---

## 📈 **Performance Tips**

1. **Selective Logging**: Only enable the logging you need
2. **Database Indexing**: The migration includes proper indexes for common queries
3. **Regular Cleanup**: Use the cleanup command or enable automatic cleanup
4. **Async Processing**: Consider queuing activity logging for high-traffic applications

---

## 🤝 **Contributing**

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## 📝 **Changelog**

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

---

## 📄 **License**

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 🏢 **About VaraAI**

**Vara-AI Tech** is a technology company specializing in Laravel packages and SaaS solutions. We're building a comprehensive ecosystem of Laravel tools to help developers build better applications faster.

### 🚀 **Our Laravel Package Ecosystem**

- **🔐 [Laravel SaaS Auth](https://github.com/VaraAI/laravel-saas-auth)** - Complete SaaS authentication system
- **📱 [VaraSMS](https://github.com/VaraAI/varasms)** - SMS integration package  
- **⚡ [Recharge Meter](https://github.com/VaraAI/recharge-meter)** - Utility management system
- **📊 [Activity Monitor](https://github.com/VaraAI/activity-monitor)** - User activity logging *(this package)*

### 🌐 **Connect with VaraAI**

- **Organization**: [github.com/VaraAI](https://github.com/VaraAI)
- **Website**: [varaai.tech](https://varaai.tech)
- **YouTube**: [Vara AI Tech](https://www.youtube.com/@vara-ai-tech)
- **Location**: Korea, South
- **Management**: iPROTE Technology Company Limited

---

## 🌟 **Credits**

- [**Vara-AI Tech**](https://varaai.tech) - Lead Developer
- [**VaraAI Organization**](https://github.com/VaraAI) - Package Maintainer  
- [**iPROTE Technology Company Limited**](https://github.com/VaraAI) - Management
- [All Contributors](https://github.com/VaraAI/activity-monitor/contributors)

### 📞 **Contact & Support**

- **🌐 Website**: [varaai.tech](https://varaai.tech)
- **📧 Email**: constantinomsigwa@gmail.com
- **🐙 GitHub**: [@VaraAI](https://github.com/VaraAI)
- **📺 YouTube**: [@vara-ai-tech](https://www.youtube.com/@vara-ai-tech)
- **🐛 Issues**: [Report Issues](https://github.com/VaraAI/activity-monitor/issues)
- **💬 Discussions**: [GitHub Discussions](https://github.com/VaraAI/activity-monitor/discussions)

---

## 🔮 **Roadmap**

- [ ] **Dashboard UI** for activity logs
- [ ] **Export functionality** (CSV, JSON)
- [ ] **Real-time broadcasting** (WebSockets)
- [ ] **Notifications** on critical actions
- [ ] **Advanced filtering** and search
- [ ] **Performance optimizations**
- [ ] **Multi-tenant support** 