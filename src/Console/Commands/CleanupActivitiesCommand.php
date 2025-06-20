<?php

namespace VaraTech\ActivityMonitor\Console\Commands;

use Illuminate\Console\Command;
use VaraTech\ActivityMonitor\Models\Activity;
use Carbon\Carbon;

class CleanupActivitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'activity-monitor:cleanup 
                           {--days= : Number of days to keep activities (overrides config)}
                           {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old activity logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('activity-monitor.cleanup.enabled', false) && !$this->option('days')) {
            $this->error('Activity cleanup is not enabled. Enable it in config or use --days option.');
            return 1;
        }

        $days = $this->option('days') ?: config('activity-monitor.cleanup.older_than_days', 90);
        $cutoffDate = Carbon::now()->subDays($days);

        $query = Activity::where('created_at', '<', $cutoffDate);
        $count = $query->count();

        if ($count === 0) {
            $this->info('No activities found to cleanup.');
            return 0;
        }

        if ($this->option('dry-run')) {
            $this->info("Would delete {$count} activities older than {$days} days ({$cutoffDate->format('Y-m-d H:i:s')})");
            return 0;
        }

        if ($this->confirm("Delete {$count} activities older than {$days} days?")) {
            $deleted = $query->delete();
            $this->info("Deleted {$deleted} activities.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
} 