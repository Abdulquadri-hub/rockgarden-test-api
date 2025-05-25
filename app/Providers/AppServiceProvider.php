<?php

namespace App\Providers;

use App\Http\Services\SaleOrderService;
use App\Http\Services\SaleOrderServiceInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SaleOrderServiceInterface::class,
            SaleOrderService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
        Gate::before(function ($user, $ability) {
          return $user->hasRole('Administrators') ? true : null;
        });
    }
}
