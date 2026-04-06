@extends('layouts.master')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #facc15 100%);
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.05);
        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }
    
    html[data-bs-theme="dark"] .stat-card, 
    html[data-bs-theme="dark"] .chart-card, 
    html[data-bs-theme="dark"] .table-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--glass-shadow);
    }

    .stat-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        background: #ffffff;
    }
    
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .stat-card:hover::after {
        opacity: 1;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .icon-primary { background: var(--primary-gradient); }
    .icon-warning { background: var(--warning-gradient); text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .icon-success { background: var(--success-gradient); }
    .icon-info { background: var(--info-gradient); }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 0.25rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-family: 'Inter', 'Cairo', sans-serif;
    }
    
    html[data-bs-theme="dark"] .stat-value {
        -webkit-text-fill-color: #f8fafc;
        background: none;
        text-shadow: 0 2px 10px rgba(255,255,255,0.1);
    }

    .stat-label {
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    html[data-bs-theme="dark"] .stat-label {
        color: #94a3b8;
    }

    .chart-card, .table-card {
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
    }
    
    .chart-card:hover, .table-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(-4px);
    }

    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 700;
        font-size: 1.15rem;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
    }
    
    html[data-bs-theme="dark"] .card-header {
        border-bottom-color: rgba(255,255,255,0.08);
        color: #f1f5f9;
    }

    .card-header i {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.25rem;
    }
    
    html[data-bs-theme="dark"] .card-header i {
        -webkit-text-fill-color: #818cf8;
    }

    .table > :not(caption) > * > * {
        padding: 1rem 1.5rem;
        border-bottom-color: rgba(0,0,0,0.05);
        vertical-align: middle;
    }
    
    html[data-bs-theme="dark"] .table > :not(caption) > * > * {
        border-bottom-color: rgba(255,255,255,0.05);
        color: #e2e8f0;
    }

    .table tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.04);
        transform: scale(1.005);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        z-index: 10;
        position: relative;
        border-radius: 0.5rem;
    }
    
    html[data-bs-theme="dark"] .table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.1);
    }

    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0.4em 0.8em;
    }
    
    .list-group-item {
        border-color: rgba(0,0,0,0.05);
        transition: all 0.2s ease;
        margin-bottom: 2px;
        border-radius: 0.5rem !important;
    }
    
    html[data-bs-theme="dark"] .list-group-item {
        border-color: rgba(255,255,255,0.05);
        background: transparent;
        color: #e2e8f0;
    }
    
    .list-group-item:hover {
        background-color: rgba(99, 102, 241, 0.04);
        padding-right: 1.5rem !important;
        transform: translateX(-4px);
    }
    
    html[data-bs-theme="dark"] .list-group-item:hover {
        background-color: rgba(99, 102, 241, 0.1);
    }
</style>
@endpush

@section('content')

{{-- بطاقات الإحصائيات --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon icon-primary">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalOrders) }}</div>
                    <div class="stat-label">إجمالي الأوامر</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon icon-warning">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($inProgress) }}</div>
                    <div class="stat-label">قيد التنفيذ</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon icon-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($completedToday) }}</div>
                    <div class="stat-label">مكتملة اليوم</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon icon-info">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalRevenue, 0) }}</div>
                    <div class="stat-label">إجمالي الإيرادات</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- الرسوم البيانية --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card chart-card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>الأوامر حسب الحالة
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="statusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card chart-card h-100">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>الأوامر اليومية (آخر 30 يوم)
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- الجداول --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-clock-history me-2"></i>آخر أوامر العمل</span>
                <span class="badge bg-secondary">{{ $recentOrders->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>رقم الأمر</th>
                            <th>العميل</th>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td>{{ $order->client->name ?? '—' }}</td>
                                <td>{{ $order->product->name ?? '—' }}</td>
                                <td>{{ number_format((float)$order->quantity, 0) }}</td>
                                <td>{{ number_format((float)$order->total_price, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status->value }} rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td style="font-size: 0.8rem;">{{ $order->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد أوامر عمل بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- أعلى المنتجات --}}
        <div class="card chart-card mb-3">
            <div class="card-header">
                <i class="bi bi-trophy me-2"></i>أعلى المنتجات
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" style="max-height: 220px;"></canvas>
            </div>
        </div>

        {{-- آخر الدفعات --}}
        <div class="card table-card">
            <div class="card-header">
                <i class="bi bi-collection me-2"></i>آخر الدفعات
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentBatches as $batch)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <div>
                            <div class="fw-semibold" style="font-size: 0.85rem;">{{ $batch->name }}</div>
                            <small class="text-muted">{{ $batch->batch_number }}</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $batch->work_orders_count }} أمر</span>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4" style="font-size: 0.85rem;">
                        لا توجد دفعات بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- الأوامر المتأخرة --}}
@if($overdueOrders->count() > 0)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card table-card border-danger border-opacity-25">
            <div class="card-header text-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>أوامر متأخرة
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>رقم الأمر</th>
                            <th>العميل</th>
                            <th>المنتج</th>
                            <th>تاريخ التسليم</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdueOrders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td>{{ $order->client->name ?? '—' }}</td>
                                <td>{{ $order->product->name ?? '—' }}</td>
                                <td class="text-danger fw-semibold">{{ $order->due_date?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status->value }} rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(function () {
    var isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    var textColor = isDark ? '#cbd5e1' : '#64748b';
    var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

    // ── مخطط الحالات (دائري) ──
    var statusLabels = @json(array_keys($statusCounts));
    var statusValues = @json(array_values($statusCounts));

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            cutout: '65%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, padding: 12, font: { size: 11, family: 'Cairo' } }
                }
            }
        }
    });

    // ── مخطط الأوامر اليومية (خطي) ──
    var trendData = @json($dailyTrend);
    var trendLabels = Object.keys(trendData);
    var trendValues = Object.values(trendData);

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'الأوامر',
                data: trendValues,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: textColor, maxRotation: 45, font: { size: 10, family: 'Cairo' } },
                    grid: { color: gridColor }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1, font: { size: 10, family: 'Cairo' } },
                    grid: { color: gridColor }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // ── مخطط أعلى المنتجات (شريطي) ──
    var topLabels = [];
    var topValues = [];
    @foreach($topProducts as $tp)
        topLabels.push('{{ $tp->product->name ?? "غير محدد" }}');
        topValues.push({{ (float)$tp->total_qty }});
    @endforeach

    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: topLabels,
            datasets: [{
                data: topValues,
                backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { color: textColor, font: { size: 10, family: 'Cairo' } },
                    grid: { color: gridColor }
                },
                y: {
                    ticks: { color: textColor, font: { size: 10, family: 'Cairo' } },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
