@extends('layouts.master')

@section('title', $client->name)
@section('page-title', 'تفاصيل العميل')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-person me-2"></i>{{ $client->name }}</h4>
    <div>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card detail-card">
            <div class="card-header">بيانات العميل</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $client->name }}</span></div>
                <div class="detail-row"><span class="detail-label">البريد</span><span class="detail-value">{{ $client->email ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">الهاتف</span><span class="detail-value">{{ $client->phone ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">العنوان</span><span class="detail-value">{{ $client->address ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">حد الائتمان</span><span class="detail-value">{{ $client->credit_limit ? number_format((float)$client->credit_limit, 2) : '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">تاريخ التسجيل</span><span class="detail-value">{{ $client->created_at?->format('d/m/Y') }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card data-table-card">
            <div class="card-header px-3 py-2 fw-semibold" style="font-size:0.9rem;">آخر أوامر العمل</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>رقم الأمر</th>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->workOrders as $order)
                            <tr>
                                <td><a href="{{ route('work-orders.show', $order) }}">{{ $order->order_number }}</a></td>
                                <td>{{ $order->product->name ?? '—' }}</td>
                                <td>{{ number_format((float)$order->quantity, 0) }}</td>
                                <td>{{ number_format((float)$order->total_price, 2) }}</td>
                                <td><span class="badge badge-{{ $order->status->value }} rounded-pill px-2 py-1" style="font-size:0.7rem;">{{ $order->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">لا توجد أوامر بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
