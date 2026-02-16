<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة دفعة — {{ $orderBatch->batch_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; font-size: 12pt; background: #fff; color: #222; padding: 2rem; }
        .print-header { text-align: center; margin-bottom: 1.5rem; border-bottom: 2px solid #333; padding-bottom: 1rem; }
        .print-header h2 { margin: 0; font-weight: 700; }
        .print-header p { margin: 0.25rem 0 0; opacity: 0.7; font-size: 10pt; }
        .info-table td { padding: 0.25rem 0.75rem; }
        .info-table .label { font-weight: 600; width: 120px; }
        .section-title { font-weight: 700; font-size: 13pt; margin: 1.5rem 0 0.5rem; border-bottom: 1px solid #ccc; padding-bottom: 0.25rem; }
        .table th { font-size: 10pt; }
        .table td { font-size: 10pt; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>طباعة</button>
    <a href="{{ route('order-batches.show', $orderBatch) }}" class="btn btn-outline-secondary">رجوع</a>
</div>

<div class="print-header">
    <h2>دفعة أوامر</h2>
    <p>{{ $orderBatch->batch_number }} — {{ $orderBatch->name }}</p>
</div>

<div class="section-title">بيانات الدفعة</div>
<table class="info-table">
    <tr><td class="label">رقم الدفعة:</td><td>{{ $orderBatch->batch_number }}</td></tr>
    <tr><td class="label">الاسم:</td><td>{{ $orderBatch->name }}</td></tr>
    <tr><td class="label">النوع:</td><td>{{ $orderBatch->type->label() }}</td></tr>
    <tr><td class="label">التاريخ:</td><td>{{ \Carbon\Carbon::parse($orderBatch->date)->format('d/m/Y') }}</td></tr>
    <tr><td class="label">الوردية:</td><td>{{ $orderBatch->shift ?? '—' }}</td></tr>
</table>

<div class="section-title">أوامر العمل ({{ $orderBatch->workOrders->count() }})</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>رقم الأمر</th>
            <th>العميل</th>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الإجمالي</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; @endphp
        @foreach($orderBatch->workOrders as $order)
            @php $grandTotal += (float)$order->total_price; @endphp
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->client->name ?? '—' }}</td>
                <td>{{ $order->product->name ?? '—' }}</td>
                <td>{{ number_format((float)$order->quantity, 0) }}</td>
                <td>{{ number_format((float)$order->unit_price, 2) }}</td>
                <td>{{ number_format((float)$order->total_price, 2) }}</td>
                <td>{{ $order->status->label() }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-start">الإجمالي الكلي</th>
            <th colspan="2">{{ number_format($grandTotal, 2) }}</th>
        </tr>
    </tfoot>
</table>

@if($orderBatch->notes)
    <div class="section-title">ملاحظات</div>
    <p>{{ $orderBatch->notes }}</p>
@endif

<div style="margin-top: 3rem; display: flex; justify-content: space-between;">
    <div style="text-align: center; width: 200px;"><div style="border-top: 1px solid #999; padding-top: 0.5rem;">توقيع المسؤول</div></div>
    <div style="text-align: center; width: 200px;"><div style="border-top: 1px solid #999; padding-top: 0.5rem;">المراجعة</div></div>
</div>

</body>
</html>
