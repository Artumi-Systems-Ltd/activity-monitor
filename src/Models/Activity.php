<?php

namespace VaraTech\ActivityMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Activity extends Model
{
    protected $fillable = [
        'action',
        'description',
        'user_id',
        'user_type',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->setTable(config('activity-monitor.database_table', 'activities'));
    }

    /**
     * Get the user that performed the activity.
     */
    public function user(): MorphTo
    {
        return $this->morphTo('user');
    }

    /**
     * Get the subject of the activity.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    /**
     * Scope activities for a specific user.
     */
    public function scopeForUser(Builder $query, $user): Builder
    {
        if (is_object($user)) {
            return $query->where('user_type', get_class($user))
                        ->where('user_id', $user->getKey());
        }

        return $query->where('user_id', $user);
    }

    /**
     * Scope activities by action.
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Get recent activities.
     */
    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope activities within date range.
     */
    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope activities for today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    /**
     * Scope activities for a specific subject.
     */
    public function scopeForSubject(Builder $query, $subject): Builder
    {
        return $query->where('subject_type', get_class($subject))
                    ->where('subject_id', $subject->getKey());
    }

    /**
     * Get a property value from the properties JSON field.
     */
    public function getProperty(string $key, $default = null)
    {
        return data_get($this->properties, $key, $default);
    }

    /**
     * Check if activity has a specific property.
     */
    public function hasProperty(string $key): bool
    {
        return array_key_exists($key, $this->properties ?? []);
    }
} 