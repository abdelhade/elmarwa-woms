<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Models\OrderBatch;
use App\Models\WorkOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalOrders = WorkOrder::count();

        $inProgress = WorkOrder::where('status', WorkOrderStatus::InProgress->value)->count();

        $completedToday = WorkOrder::where('status', WorkOrderStatus::Completed->value)
            ->whereDate('updated_at', Carbon::today())
            ->count();

        $totalRevenue = (float) WorkOrder::sum('total_price');

        $recentOrders = WorkOrder::with(['client', 'product'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $overdueOrders = WorkOrder::with(['client', 'product'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today())
            ->whereNotIn('status', [
                WorkOrderStatus::Completed->value,
                WorkOrderStatus::Cancelled->value,
            ])
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $statusCounts = [];
        foreach (WorkOrderStatus::cases() as $status) {
            $statusCounts[$status->label()] = WorkOrder::where('status', $status->value)->count();
        }

        $dailyTrend = WorkOrder::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as count")
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $topProducts = WorkOrder::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $recentBatches = OrderBatch::withCount('workOrders')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalOrders',
            'inProgress',
            'completedToday',
            'totalRevenue',
            'recentOrders',
            'overdueOrders',
            'statusCounts',
            'dailyTrend',
            'topProducts',
            'recentBatches'
        ));
    }
}
