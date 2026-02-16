@extends('layouts.master')

@section('title', $category->name)
@section('page-title', 'تفاصيل التصنيف')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-diagram-3 me-2"></i>{{ $category->name }}</h4>
    <div>
        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card detail-card">
            <div class="card-header">بيانات التصنيف</div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $category->name }}</span></div>
                <div class="detail-row"><span class="detail-label">التصنيف الأب</span><span class="detail-value">{{ $category->parent->name ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">الوصف</span><span class="detail-value">{{ $category->description ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">تاريخ الإنشاء</span><span class="detail-value">{{ $category->created_at?->format('d/m/Y H:i') }}</span></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        {{-- Children --}}
        <div class="card detail-card mb-3">
            <div class="card-header">التصنيفات الفرعية ({{ $category->children->count() }})</div>
            <div class="card-body p-0">
                @forelse($category->children as $child)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <span>{{ $child->name }}</span>
                        <a href="{{ route('categories.show', $child) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 mb-0">لا توجد تصنيفات فرعية.</p>
                @endforelse
            </div>
        </div>

        {{-- Products --}}
        <div class="card detail-card">
            <div class="card-header">المنتجات ({{ $category->products->count() }})</div>
            <div class="card-body p-0">
                @forelse($category->products as $product)
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <span>{{ $product->name }} <small class="text-muted">({{ $product->sku }})</small></span>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    </div>
                @empty
                    <p class="text-muted text-center py-3 mb-0">لا توجد منتجات في هذا التصنيف.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
