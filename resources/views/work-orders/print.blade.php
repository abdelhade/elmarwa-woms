<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة أمر عمل — {{ $workOrder->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; font-size: 13pt; background: #fff; color: #222; padding: 2rem; }
        .print-header { text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #333; padding-bottom: 1rem; }
        .print-header h2 { margin: 0; font-weight: 700; }
        .print-header p { margin: 0.25rem 0 0; opacity: 0.7; font-size: 11pt; }
        .info-table td { padding: 0.3rem 0.75rem; }
        .info-table .label { font-weight: 600; width: 140px; }
        .section-title { font-weight: 700; font-size: 14pt; margin: 1.5rem 0 0.5rem; border-bottom: 1px solid #ccc; padding-bottom: 0.25rem; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .print-page { page-break-after: always; }
            .print-page:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4 d-flex justify-content-center align-items-center gap-2">
    <label for="copyCount" class="fw-bold mb-0">عدد النسخ:</label>
    <input type="number" id="copyCount" class="form-control text-center" style="width: 80px;" value="1" min="1" max="20" onchange="generateCopies()">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> طباعة</button>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary">رجوع</a>
</div>

<div id="renderContainer"></div>

<template id="documentTemplate">
    <div class="print-page">
        <div class="print-header">
    <h2>أمر عمل</h2>
    <p>{{ $workOrder->order_number }} — {{ $workOrder->created_at?->format('d/m/Y') }}</p>
</div>

<div class="section-title">بيانات الأمر</div>
<table class="info-table">
    <tr><td class="label">رقم الأمر:</td><td>{{ $workOrder->order_number }}</td></tr>
    <tr><td class="label">العميل:</td><td>{{ $workOrder->client->name ?? '—' }}</td></tr>
    <tr><td class="label">المنتج:</td><td>{{ $workOrder->product->name ?? '—' }}</td></tr>
    <tr><td class="label">التصنيف:</td><td>{{ $workOrder->product->category->name ?? '—' }}</td></tr>
    <tr><td class="label">الحالة:</td><td>{{ $workOrder->status->label() }}</td></tr>
</table>

<div class="section-title">التفاصيل المالية</div>
<table class="info-table">
    <tr><td class="label">الكمية:</td><td>{{ number_format((float)$workOrder->quantity, 2) }}</td></tr>
    <tr><td class="label">سعر الوحدة:</td><td>{{ number_format((float)$workOrder->unit_price, 2) }}</td></tr>
    <tr><td class="label">الإجمالي:</td><td><strong>{{ number_format((float)$workOrder->total_price, 2) }}</strong></td></tr>
    <tr><td class="label">تاريخ التسليم:</td><td>{{ $workOrder->due_date?->format('d/m/Y') ?? '—' }}</td></tr>
</table>

@if($workOrder->notes)
    <div class="section-title">ملاحظات</div>
    <p>{{ $workOrder->notes }}</p>
@endif

@if($workOrder->orderBatch)
    <div class="section-title">الدفعة</div>
    <p>{{ $workOrder->orderBatch->name }} ({{ $workOrder->orderBatch->batch_number }})</p>
@endif

<div style="margin-top: 3rem; display: flex; justify-content: space-between;">
    <div style="text-align: center; width: 200px;"><div style="border-top: 1px solid #999; padding-top: 0.5rem;">توقيع المسؤول</div></div>
    <div style="text-align: center; width: 200px;"><div style="border-top: 1px solid #999; padding-top: 0.5rem;">توقيع العميل</div></div>
</div>
    </div>
</template>

<script>
    function generateCopies() {
        const container = document.getElementById('renderContainer');
        const template = document.getElementById('documentTemplate').innerHTML;
        const count = parseInt(document.getElementById('copyCount').value) || 1;
        
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            container.innerHTML += template;
        }
    }
    
    // Generate the first initial copy on page load
    window.onload = generateCopies;
</script>

</body>
</html>
