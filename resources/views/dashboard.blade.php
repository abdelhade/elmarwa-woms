@extends('layouts.master')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

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
