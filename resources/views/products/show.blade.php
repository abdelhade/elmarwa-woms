@extends('layouts.master')

@section('title', $product->name)
@section('page-title', 'تفاصيل المنتج')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-box-seam me-2"></i>{{ $product->name }}</h4>
    <div>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card detail-card">
            <div class="card-header">البيانات الأساسية</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $product->name }}</span></div>
                <div class="detail-row"><span class="detail-label">SKU</span><span class="detail-value"><code>{{ $product->sku }}</code></span></div>
                <div class="detail-row"><span class="detail-label">التصنيف</span><span class="detail-value">{{ $product->category->name ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">وحدة القياس</span><span class="detail-value">{{ $product->uom->label() }}</span></div>
                <div class="detail-row"><span class="detail-label">سعر الوحدة</span><span class="detail-value">{{ number_format((float)$product->unit_price, 2) }}</span></div>
                <div class="detail-row"><span class="detail-label">وزن الوحدة</span><span class="detail-value">{{ $product->unit_weight ? number_format((float)$product->unit_weight, 3) . ' كجم' : '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">نسبة الهالك</span><span class="detail-value">{{ $product->waste_rate }}%</span></div>
                <div class="detail-row"><span class="detail-label">مدة التوريد</span><span class="detail-value">{{ $product->lead_time_days ? $product->lead_time_days . ' يوم' : '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">الوصف</span><span class="detail-value">{{ $product->description ?? '—' }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card detail-card">
            <div class="card-header">المواصفات</div>
            <div class="card-body">
                @if($product->specifications && count($product->specifications) > 0)
                    @foreach($product->specifications as $key => $value)
                        @if($value)
                            <div class="detail-row">
                                <span class="detail-label">{{ $key }}</span>
                                <span class="detail-value">{{ $value }}</span>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-muted text-center py-3 mb-0">لا توجد مواصفات.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
