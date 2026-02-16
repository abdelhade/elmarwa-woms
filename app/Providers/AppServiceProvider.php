<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\OrderBatchRepositoryInterface;
use App\Repositories\Contracts\WorkOrderRepositoryInterface;
use App\Repositories\Eloquent\EloquentOrderBatchRepository;
use App\Repositories\Eloquent\EloquentWorkOrderRepository;
use App\Services\Reporting\DetailedReportStrategy;
use App\Services\Reporting\ReportStrategyInterface;
use App\Services\ReportingService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            WorkOrderRepositoryInterface::class,
            EloquentWorkOrderRepository::class
        );

        $this->app->bind(
            OrderBatchRepositoryInterface::class,
            EloquentOrderBatchRepository::class
        );

        $this->app->bind(ReportStrategyInterface::class, DetailedReportStrategy::class);

        $this->app->bind(ReportingService::class, function ($app) {
            return new ReportingService(
                $app->make(ReportStrategyInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Register Blade directives for permissions
        \Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        \Blade::if('admin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });
    }
}
