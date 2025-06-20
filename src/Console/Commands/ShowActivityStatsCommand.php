<?php

namespace VaraTech\ActivityMonitor\Console\Commands;

use Illuminate\Console\Command;
use VaraTech\ActivityMonitor\Models\Activity;
use Carbon\Carbon;

class ShowActivityStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'activity-monitor:stats 
                           {--days=30 : Number of days to show stats for}
                           {--user= : Show stats for specific user ID}';

    /**
     * The console command description.
     */
    protected $description = 'Show activity statistics';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = $this->option('days');
        $userId = $this->option('user');
        
        $query = Activity::query();
        
        if ($days) {
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $totalActivities = $query->count();
        $this->info("Total Activities: {$totalActivities}");
        
        // Top actions
        $this->line('');
        $this->info('Top Actions:');
        $topActions = $query->select('action')
            ->selectRaw('count(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $this->table(['Action', 'Count'], $topActions->map(function ($item) {
            return [$item->action, $item->count];
        })->toArray());

        // Top users
        if (!$userId) {
            $this->line('');
            $this->info('Top Users:');
            $topUsers = $query->select('user_id')
                ->selectRaw('count(*) as count')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            $this->table(['User ID', 'Count'], $topUsers->map(function ($item) {
                return [$item->user_id, $item->count];
            })->toArray());
        }

        // Daily activity (last 7 days)
        $this->line('');
        $this->info('Daily Activity (Last 7 Days):');
        $dailyActivity = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Activity::whereDate('created_at', $date)->count();
            $dailyActivity[] = [$date->format('Y-m-d'), $count];
        }

        $this->table(['Date', 'Activities'], $dailyActivity);

        return 0;
    }
} 