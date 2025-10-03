<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure API rate limiter to fix the MongoDB API authentication issue
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many requests. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Auto-fix storage symlink in development environment
        if ($this->app->environment('local')) {
            $this->ensureStorageSymlink();
        }
    }

    /**
     * Ensure storage symlink exists (Windows compatible)
     */
    private function ensureStorageSymlink()
    {
        $linkPath = public_path('storage');
        
        // Only check/fix if symlink is missing or broken (avoid doing this on every request)
        if (!is_link($linkPath) || !is_dir($linkPath)) {
            try {
                $targetPath = storage_path('app/public');
                
                // Remove broken symlink if exists
                if (file_exists($linkPath) || is_link($linkPath)) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        exec("rmdir /s /q \"$linkPath\" 2>nul");
                    } else {
                        unlink($linkPath);
                    }
                }
                
                // Create new symlink
                if (PHP_OS_FAMILY === 'Windows') {
                    $relativePath = '..\\storage\\app\\public';
                    $command = "mklink /D \"$linkPath\" \"$relativePath\"";
                    exec($command);
                } else {
                    symlink($targetPath, $linkPath);
                }
                
                \Log::info('Storage symlink auto-fixed');
            } catch (\Exception $e) {
                \Log::warning('Failed to auto-fix storage symlink: ' . $e->getMessage());
            }
        }
    }
}
