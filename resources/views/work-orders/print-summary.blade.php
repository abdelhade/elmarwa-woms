<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة مجمع أمر عمل — {{ $workOrder->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @page { margin: 0; size: 75mm auto; }
        body { font-family: 'Cairo', sans-serif; font-size: 12pt; font-weight: 600; background: #fff; color: #000; margin: 0 auto; padding: 15px; width: 75mm; max-width: 75mm; box-sizing: border-box; }
        .print-header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .print-header h2 { margin: 0; font-size: 16pt; font-weight: 700; }
        .print-header p { margin: 2px 0 0; font-size: 11pt; }
        .info-table { width: 100%; font-size: 11pt; margin-bottom: 10px; border: none; }
        .info-table td { padding: 2px 0; border: none; }
        .info-table .label { font-weight: 700; width: 45%; }
        .section-title { font-weight: 700; font-size: 13pt; margin: 10px 0 5px; border-bottom: 1px dashed #000; padding-bottom: 2px; text-align: center; }
        
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 5px; font-size: 11pt; }
        .summary-table th { border-bottom: 1px dashed #000; padding: 3px 0; text-align: right; font-weight: 700; }
        .summary-table td { padding: 5px 0; text-align: right; vertical-align: top; border-bottom: 1px dotted #999; line-height: 1.4; }
        .summary-table .qty-col { text-align: center; width: 40px; font-weight: bold; }
        
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
    <input type="number" id="copyCount" class="form-control text-center" style="width: 80px;" value="{{ \App\Helpers\Setting::get('default_print_copies', 1) }}" min="1" max="20" onchange="generateCopies()">
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> طباعة</button>
    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary">رجوع</a>
</div>

<div id="renderContainer"></div>

<template id="documentTemplate">
    <div class="print-page">
        <div class="print-header">
    <h2>أمر عمل (مجمع بالتصنيف)</h2>
    <p>{{ $workOrder->order_number }} — {{ $workOrder->created_at?->format('d/m/Y') }}</p>
</div>

<div class="section-title">بيانات العميل والأمر</div>
<table class="info-table">
    <tr><td class="label">رقم الأمر:</td><td>{{ $workOrder->order_number }}</td></tr>
    <tr><td class="label">العميل:</td><td>{{ $workOrder->client->name ?? '—' }}</td></tr>
    <tr><td class="label">تاريخ الأمر:</td><td>{{ $workOrder->created_at?->format('d/m/Y') }}</td></tr>
</table>

<div class="section-title">إجمالي الكميات حسب التصنيف</div>
<table class="summary-table">
    <thead>
        <tr>
            <th>التصنيف</th>
            <th class="qty-col">الكمية</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $category => $totalQuantity)
            <tr>
                <td>{{ $category }}</td>
                <td class="qty-col">{{ number_format($totalQuantity, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td style="border-bottom: none; border-top: 1px dashed #000; padding-top: 5px;" class="fw-bold fs-6">الإجمالي:</td>
            <td style="border-bottom: none; border-top: 1px dashed #000; padding-top: 5px;" class="qty-col fs-6">{{ number_format($summary->sum(), 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($workOrder->notes)
    <div class="section-title">ملاحظات</div>
    <p>{{ $workOrder->notes }}</p>
@endif

<div style="margin-top: 30px; text-align: center; border-top: 1px dashed #000; padding-top: 10px; font-size: 11pt;">
    <p style="margin: 0;">توقيع مراقبة المخازن: .......................</p>
    <p style="margin: 5px 0 0;">توقيع مدير الإنتاج: .......................</p>
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
