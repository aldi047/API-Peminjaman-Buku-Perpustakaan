<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->group(['prefix' => 'api',
        'namespace' => 'App\Http\Controllers'], function ($router) {
            require base_path('routes/api.php');
        });
    }
}