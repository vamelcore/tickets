<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * All the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        \App\Contracts\Api\V1\AuthInterface::class => \App\Services\Api\V1\AuthService::class,
    ];

    /**
     * All the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [

    ];
}
