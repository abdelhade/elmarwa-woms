@extends('layouts.master')

@section('title', 'تعديل التصنيف')
@section('page-title', 'تعديل التصنيف')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>تعديل: {{ $category->name }}</h4>
    <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i>رجوع
    </a>
</div>

@include('partials.alerts')

<div class="card form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $category->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="parent_id" class="form-label">التصنيف الأب</label>
                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                        <option value="">— بدون (تصنيف رئيسي) —</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <hr class="my-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5><i class="bi bi-list-stars me-2"></i>خصائص الصنف (Attributes)</h5>
                        <button type="button" id="add-attribute" class="btn btn-sm btn-success">
                            <i class="bi bi-plus-lg me-1"></i>إضافة خاصية
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="attributes-table">
                            <thead class="table-light">
                                <tr>
                                    <th>اسم الخاصية (مثلاً: الطول)</th>
                                    <th style="width: 200px;">نوع الإدخال</th>
                                    <th style="width: 80px;">حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $attributes = old('attributes', $category->attributes);
                                @endphp
                                <tr class="empty-row {{ count($attributes) > 0 ? 'd-none' : '' }}">
                                    <td colspan="3" class="text-center text-muted py-3">لم يتم إضافة خصائص بعد.</td>
                                </tr>
                                @foreach($attributes as $index => $attr)
                                    <tr>
                                        <td>
                                            <input type="text" name="attributes[{{ $index }}][name]" class="form-control" 
                                                   value="{{ is_array($attr) ? $attr['name'] : $attr->name }}" required>
                                        </td>
                                        <td>
                                            @php $type = is_array($attr) ? $attr['type'] : $attr->type; @endphp
                                            <select name="attributes[{{ $index }}][type]" class="form-select">
                                                <option value="text" {{ $type == 'text' ? 'selected' : '' }}>نص (Text)</option>
                                                <option value="number" {{ $type == 'number' ? 'selected' : '' }}>رقم (Number)</option>
                                                <option value="select" {{ $type == 'select' ? 'selected' : '' }}>قائمة (Select)</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-check-lg me-1"></i>تحديث البيانات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let attributeIndex = {{ count($attributes) }};

    $('#add-attribute').click(function() {
        $('.empty-row').addClass('d-none');
        
        let row = `
            <tr>
                <td>
                    <input type="text" name="attributes[${attributeIndex}][name]" class="form-control" placeholder="مثلاً: الطول، العرض، اللون..." required>
                </td>
                <td>
                    <select name="attributes[${attributeIndex}][type]" class="form-select">
                        <option value="text">نص (Text)</option>
                        <option value="number">رقم (Number)</option>
                        <option value="select">قائمة (Select)</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        
        $('#attributes-table tbody').append(row);
        attributeIndex++;
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#attributes-table tbody tr').not('.empty-row').length === 0) {
            $('.empty-row').removeClass('d-none');
        }
    });
});
</script>
@endpush
