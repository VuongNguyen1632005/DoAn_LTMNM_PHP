<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        RateLimiter::for('login', function (Request $request) {
            $identifier = $request->input('login', $request->input('username', ''));
            $key = Str::transliterate(Str::lower($identifier) . '|' . $request->ip());

            return Limit::perMinute(5)->by($key)->response(function () {
                return response('Quá nhiều lần thử đăng nhập. Vui lòng thử lại sau 1 phút.', 429);
            });
        });
    }
}
