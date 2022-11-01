<?php
/*
 * dezsidog
 *
 */

namespace Cola\Hector;


use Illuminate\Auth\AuthManager;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        config([
            'auth.guards.hector' => [
                'driver' => 'hector',
                'provider' => 'users',
            ],
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../config/hector.php', 'hector'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/hector.php' => config_path('hector.php'),
            ]);
        }

        Auth::resolved(function (AuthManager $auth) {
            $auth->extend('hector', function (Application $app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard) use($app) {
                    $app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    private function createGuard(AuthManager $auth, array $config): RequestGuard
    {
        return new RequestGuard(
            new Guard(config('hector.key'), config('hector.algorithm')),
            $this->app['request'],
            $auth->createUserProvider($config['provider']),
        );
    }
}
