<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة مجمع أمر عمل — {{ $workOrder->order_number }}</title>
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
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 0.75rem; text-align: right; }
        .summary-table th { background-color: #f8f9fa; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4">
    <button class="btn btn-primary btn-print" onclick="window.print()">طباعة</button>
    <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">رجوع</a>
</div>

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
            <th class="text-center">إجمالي الكمية</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $category => $totalQuantity)
            <tr>
                <td>{{ $category }}</td>
                <td class="text-center fw-bold">{{ number_format($totalQuantity, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="fw-bold fs-5">
            <td>الإجمالي العام للكميات:</td>
            <td class="text-center">{{ number_format($summary->sum(), 2) }}</td>
        </tr>
    </tfoot>
</table>

@if($workOrder->notes)
    <div class="section-title">ملاحظات</div>
    <p>{{ $workOrder->notes }}</p>
@endif

<div style="margin-top: 5rem; display: flex; justify-content: space-between;">
    <div style="text-align: center; width: 250px;"><div style="border-top: 1px solid #000; padding-top: 0.5rem;">توقيع مراقبة المخازن</div></div>
    <div style="text-align: center; width: 250px;"><div style="border-top: 1px solid #000; padding-top: 0.5rem;">توقيع مدير الإنتاج</div></div>
</div>

</body>
</html>
