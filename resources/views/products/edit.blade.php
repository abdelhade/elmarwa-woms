@extends('layouts.master')

@section('title', 'تعديل المنتج')
@section('page-title', 'تعديل المنتج')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>تعديل: {{ $product->name }}</h4>
    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
</div>

@include('partials.alerts')

<div class="card form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">التصنيف <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">اختر...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="uom" class="form-label">وحدة القياس <span class="text-danger">*</span></label>
                    <select name="uom" id="uom" class="form-select @error('uom') is-invalid @enderror" required>
                        @foreach($units as $unit)
                            <option value="{{ $unit->value }}" {{ old('uom', $product->uom->value) == $unit->value ? 'selected' : '' }}>{{ $unit->label() }}</option>
                        @endforeach
                    </select>
                    @error('uom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="unit_price" class="form-label">سعر الوحدة <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $product->unit_price) }}" required>
                    @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="unit_weight" class="form-label">وزن الوحدة (كجم)</label>
                    <input type="number" step="0.001" name="unit_weight" id="unit_weight" class="form-control @error('unit_weight') is-invalid @enderror" value="{{ old('unit_weight', $product->unit_weight) }}">
                    @error('unit_weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="waste_rate" class="form-label">نسبة الهالك (%)</label>
                    <input type="number" step="0.01" name="waste_rate" id="waste_rate" class="form-control @error('waste_rate') is-invalid @enderror" value="{{ old('waste_rate', $product->waste_rate) }}">
                    @error('waste_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="lead_time_days" class="form-label">مدة التوريد (أيام)</label>
                    <input type="number" name="lead_time_days" id="lead_time_days" class="form-control @error('lead_time_days') is-invalid @enderror" value="{{ old('lead_time_days', $product->lead_time_days) }}">
                    @error('lead_time_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12"><hr><h6 class="fw-bold"><i class="bi bi-gear-wide-connected me-2"></i>المواصفات الفنية</h6></div>
                <div class="col-12">
                    <div id="dynamic-attributes-container" class="row g-3">
                        <div class="col-12 text-muted small">جاري تحميل المواصفات...</div>
                    </div>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const existingSpecs = @json($product->specifications ?? []);

    $('#category_id').change(function() {
        let categoryId = $(this).val();
        let $container = $('#dynamic-attributes-container');
        
        if (!categoryId) {
            $container.html('<div class="col-12 text-muted small">يرجى اختيار التصنيف أولاً لعرض المواصفات الخاصة به.</div>');
            return;
        }

        $container.html('<div class="col-12"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> جاري تحميل المواصفات...</div>');

        $.get(`/categories/${categoryId}/attributes`, function(attributes) {
            $container.empty();
            if (attributes.length === 0) {
                $container.html('<div class="col-12 text-muted small">لا توجد مواصفات إضافية لهذا التصنيف.</div>');
                return;
            }

            attributes.forEach(function(attr) {
                let value = existingSpecs[attr.name] || '';
                let inputHtml = '';
                if (attr.type === 'number') {
                    inputHtml = `<input type="number" step="any" name="specifications[${attr.name}]" class="form-control" value="${value}" placeholder="${attr.name}">`;
                } else {
                    inputHtml = `<input type="text" name="specifications[${attr.name}]" class="form-control" value="${value}" placeholder="${attr.name}">`;
                }

                let fieldHtml = `
                    <div class="col-md-4">
                        <label class="form-label">${attr.name}</label>
                        ${inputHtml}
                    </div>
                `;
                $container.append(fieldHtml);
            });
        }).fail(function() {
            $container.html('<div class="col-12 text-danger small">خطأ أثناء تحميل المواصفات.</div>');
        });
    });

    // Initial load
    if ($('#category_id').val()) {
        $('#category_id').trigger('change');
    }
});
</script>
@endpush
