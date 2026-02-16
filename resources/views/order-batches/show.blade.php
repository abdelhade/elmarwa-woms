@extends('layouts.master')

@section('title', $orderBatch->name)
@section('page-title', 'تفاصيل الدفعة')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-collection me-2"></i>{{ $orderBatch->name }}</h4>
    <div>
        <a href="{{ route('order-batches.print', $orderBatch) }}" class="btn btn-info" target="_blank"><i class="bi bi-printer me-1"></i>طباعة</a>
        <a href="{{ route('order-batches.edit', $orderBatch) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('order-batches.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card detail-card">
            <div class="card-header">بيانات الدفعة</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">رقم الدفعة</span><span class="detail-value"><code>{{ $orderBatch->batch_number }}</code></span></div>
                <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $orderBatch->name }}</span></div>
                <div class="detail-row"><span class="detail-label">النوع</span><span class="detail-value">{{ $orderBatch->type->label() }}</span></div>
                <div class="detail-row"><span class="detail-label">التاريخ</span><span class="detail-value">{{ \Carbon\Carbon::parse($orderBatch->date)->format('d/m/Y') }}</span></div>
                <div class="detail-row"><span class="detail-label">الوردية</span><span class="detail-value">{{ $orderBatch->shift ?? '—' }}</span></div>
                @if($orderBatch->notes)
                    <div class="detail-row"><span class="detail-label">ملاحظات</span><span class="detail-value">{{ $orderBatch->notes }}</span></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card data-table-card">
            <div class="card-header px-3 py-2 fw-semibold d-flex justify-content-between" style="font-size:0.9rem;">
                <span>أوامر العمل في هذه الدفعة</span>
                <span class="badge bg-primary">{{ $orderBatch->workOrders->count() }}</span>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderBatch->workOrders as $order)
                            <tr>
                                <td><a href="{{ route('work-orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>{{ $order->client->name ?? '—' }}</td>
                                <td>{{ $order->product->name ?? '—' }}</td>
                                <td>{{ number_format((float)$order->quantity, 0) }}</td>
                                <td>{{ number_format((float)$order->total_price, 2) }}</td>
                                <td><span class="badge badge-{{ $order->status->value }} rounded-pill px-2 py-1" style="font-size:0.7rem;">{{ $order->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">لا توجد أوامر في هذه الدفعة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
