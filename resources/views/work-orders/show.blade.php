@extends('layouts.master')

@section('title', $workOrder->order_number)
@section('page-title', 'تفاصيل أمر العمل')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-clipboard2-check me-2"></i>{{ $workOrder->order_number }}</h4>
    <div>
        <a href="{{ route('work-orders.print-detailed', $workOrder) }}" class="btn btn-info" target="_blank"><i class="bi bi-printer me-1"></i>طباعة مفصل</a>
        <a href="{{ route('work-orders.print-summary', $workOrder) }}" class="btn btn-secondary" target="_blank"><i class="bi bi-printer-fill me-1"></i>طباعة مجمع</a>
        <a href="{{ route('work-orders.duplicate', $workOrder) }}" class="btn btn-primary"><i class="bi bi-files me-1"></i>تكرار الطلب</a>
        <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card detail-card">
            <div class="card-header">بيانات الأمر</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">رقم الأمر</span><span class="detail-value fw-bold">{{ $workOrder->order_number }}</span></div>
                <div class="detail-row"><span class="detail-label">الحالة</span><span class="detail-value"><span class="badge badge-{{ $workOrder->status->value }} rounded-pill px-2 py-1">{{ $workOrder->status->label() }}</span></span></div>
                <div class="detail-row"><span class="detail-label">العميل</span><span class="detail-value"><a href="{{ route('clients.show', $workOrder->client_id) }}">{{ $workOrder->client->name ?? '—' }}</a></span></div>
                <div class="detail-row"><span class="detail-label">المنتج</span><span class="detail-value"><a href="{{ route('products.show', $workOrder->product_id) }}">{{ $workOrder->product->name ?? '—' }}</a></span></div>
                <div class="detail-row"><span class="detail-label">التصنيف</span><span class="detail-value">{{ $workOrder->product->category->name ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">الموظف</span><span class="detail-value">{{ $workOrder->employee->name ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">المشغل</span><span class="detail-value">{{ $workOrder->operator->name ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">مسؤول التشغيل</span><span class="detail-value">{{ $workOrder->supervisor->name ?? '—' }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card detail-card">
            <div class="card-header">التفاصيل المالية</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">الكمية</span><span class="detail-value">{{ number_format((float)$workOrder->quantity, 2) }}</span></div>
                <div class="detail-row"><span class="detail-label">سعر الوحدة</span><span class="detail-value">{{ number_format((float)$workOrder->unit_price, 2) }}</span></div>
                <div class="detail-row"><span class="detail-label">الإجمالي</span><span class="detail-value fw-bold text-success">{{ number_format((float)$workOrder->total_price, 2) }}</span></div>
                <div class="detail-row"><span class="detail-label">تاريخ التسليم</span><span class="detail-value">{{ $workOrder->due_date?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">تاريخ الإنشاء</span><span class="detail-value">{{ $workOrder->created_at?->format('d/m/Y H:i') }}</span></div>
                @if($workOrder->orderBatch)
                    <div class="detail-row"><span class="detail-label">الدفعة</span><span class="detail-value"><a href="{{ route('order-batches.show', $workOrder->orderBatch) }}">{{ $workOrder->orderBatch->name }}</a></span></div>
                @endif
            </div>
        </div>
    </div>
    @if($workOrder->notes)
        <div class="col-12">
            <div class="card detail-card">
                <div class="card-header">ملاحظات</div>
                <div class="card-body" style="font-size:0.9rem;">{{ $workOrder->notes }}</div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="card detail-card shadow-sm border-0">
            <div class="card-header bg-light fw-bold"><i class="bi bi-box-seam me-2"></i>أصناف أمر العمل</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>الصنف</th>
                                <th>الخصائص</th>
                                <th style="width: 15%;">التقدم</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-end">سعر الوحدة</th>
                                <th class="text-end">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrder->details as $idx => $detail)
                                <tr>
                                    <td class="text-center text-muted small align-middle">{{ $idx + 1 }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $detail->product->name }}</strong>
                                        <div class="text-muted small">{{ $detail->product->sku }}</div>
                                    </td>
                                    <td class="align-middle">
                                        @if($detail->attributes && count($detail->attributes) > 0)
                                            @foreach($detail->attributes as $attr)
                                                <span class="badge bg-light text-dark border me-1" style="font-size: 0.75rem;">
                                                    {{ $attr['name'] }}{{ !empty($attr['value']) ? ': ' . $attr['value'] : '' }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">لا يوجد خصائص</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $percent = $detail->quantity > 0 ? ($detail->produced_quantity / $detail->quantity) * 100 : 0;
                                            $percent = min(100, max(0, $percent));
                                            $barColor = $percent >= 100 ? 'bg-success' : ($percent > 0 ? 'bg-primary' : 'bg-secondary');
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="small text-muted" style="font-size: 0.7rem; min-width: 30px;">{{ round($percent) }}%</span>
                                        </div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            تم إنتاج {{ number_format($detail->produced_quantity, 0) }} من {{ number_format($detail->quantity, 0) }}
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">{{ number_format($detail->quantity, 2) }}</td>
                                    <td class="text-end align-middle">{{ number_format($detail->unit_price, 2) }}</td>
                                    <td class="text-end align-middle fw-bold">{{ number_format($detail->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-start fw-bold">الإجمالي الكلي</td>
                                <td class="text-center fw-bold">{{ number_format($workOrder->quantity, 2) }}</td>
                                <td></td>
                                <td class="text-end fw-bold text-success">{{ number_format($workOrder->total_price, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection