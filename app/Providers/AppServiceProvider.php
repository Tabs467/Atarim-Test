<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        RateLimiter::for('encode', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests, please wait before trying again',
                ], 429);
            });
        });

        RateLimiter::for('decode', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests, please wait before trying again',
                ], 429);
            });
        });
    }
}
