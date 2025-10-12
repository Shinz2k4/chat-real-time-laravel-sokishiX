<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PerformanceStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display cache and performance statistics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('📊 Laravel Chat App Performance Statistics');
        $this->line('');

        // Cache statistics
        $this->info('🗄️  Cache Statistics:');
        $cacheStats = CacheService::getCacheStats();
        
        if (isset($cacheStats['driver'])) {
            $this->line("   Driver: {$cacheStats['driver']}");
        } else {
            foreach ($cacheStats as $key => $value) {
                $this->line("   " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}");
            }
        }

        // Database statistics
        $this->line('');
        $this->info('🗃️  Database Statistics:');
        
        try {
            $userCount = DB::connection('mongodb')->collection('users')->count();
            $messageCount = DB::connection('mongodb')->collection('messages')->count();
            $unreadCount = DB::connection('mongodb')->collection('messages')->where('read', false)->count();
            
            $this->line("   Total Users: {$userCount}");
            $this->line("   Total Messages: {$messageCount}");
            $this->line("   Unread Messages: {$unreadCount}");
        } catch (\Exception $e) {
            $this->error("   Database connection error: " . $e->getMessage());
        }

        // Configuration check
        $this->line('');
        $this->info('⚙️  Configuration Check:');
        
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        
        $this->line("   Cache Driver: {$cacheDriver} " . ($cacheDriver === 'redis' ? '✅' : '❌'));
        $this->line("   Session Driver: {$sessionDriver} " . ($sessionDriver === 'redis' ? '✅' : '❌'));
        
        if ($cacheDriver !== 'redis' || $sessionDriver !== 'redis') {
            $this->warn('   ⚠️  Consider using Redis for better performance');
        }

        // Memory usage
        $this->line('');
        $this->info('💾 Memory Usage:');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        $this->line("   Current: " . $this->formatBytes($memoryUsage));
        $this->line("   Peak: " . $this->formatBytes($memoryPeak));

        $this->line('');
        $this->info('✅ Performance check completed!');

        return 0;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

