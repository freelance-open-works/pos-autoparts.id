<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

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
        // for optimize in development log every query
        // if (app()->isProduction() == false) {
        //     DB::listen(function ($query) {
        //         Log::info(
        //             $query->sql,
        //             [
        //                 'raw' => $query->toRawSql(),
        //                 'bindings' => $query->bindings,
        //                 'time' => $query->time,
        //                 'connectionName' => $query->connectionName,
        //             ]
        //         );
        //     });
        // }
    }
}
