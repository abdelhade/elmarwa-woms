@extends('layouts.master')

@section('title', 'خصائص الصنف')
@section('page-title', 'خصائص الصنف')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h4><i class="bi bi-list-stars me-2"></i>خصائص الصنف</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة (F4)
        </button>
        <a href="{{ route('attributes.create') }}" class="btn btn-primary" id="btnAddNew">
            <i class="bi bi-plus-lg me-1"></i>إضافة خاصية (F3)
        </a>
    </div>
</div>

{{-- Alerts --}}
@include('partials.alerts')

{{-- Filters --}}
<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('attributes.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">بحث بالاسم</label>
                    <input type="text" name="search" class="form-control" placeholder="ابحث..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع الخاصية</label>
                    <select name="type" class="form-select">
                        <option value="">الكل</option>
                        @foreach($types as $key => $value)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>بحث</button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary btn-reset-filter"><i class="bi bi-x-lg me-1"></i>مسح</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Table --}}
<div class="card data-table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>خيارات</th>
                    <th>إجباري</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attributes as $attribute)
                    <tr>
                        <td>{{ $attribute->id }}</td>
                        <td class="fw-semibold">{{ $attribute->name }}</td>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $types[$attribute->type] ?? $attribute->type }}
                            </span>
                        </td>
                        <td>
                            @if($attribute->type === 'select' && $attribute->options)
                                @foreach($attribute->options as $option)
                                    <span class="badge bg-secondary opacity-75">{{ $option }}</span>
                                @endforeach
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($attribute->is_required)
                                <span class="badge bg-danger">نعم</span>
                            @else
                                <span class="badge bg-light text-dark">لا</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('attributes.edit', $attribute) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('attributes.destroy', $attribute) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $attribute->name }}" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-list-stars d-block"></i>
                                <p>لا توجد خصائص صنف بعد.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attributes->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $attributes->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-reset-filter').on('click', function() {
        window.location.href = "{{ route('attributes.index') }}";
    });

    $('.btn-delete').on('click', function() {
        const name = $(this).data('name');
        if (confirm(`هل أنت متأكد من حذف الخاصية "${name}"؟`)) {
            $(this).closest('form').submit();
        }
    });

    // اختصار F3 للإضافة الجديدة
    $(document).on('keydown', function(e) {
        if (e.key === "F3") {
            e.preventDefault();
            const btn = document.getElementById('btnAddNew');
            if (btn) btn.click();
        }

        // F4 Shortcut for Print
        if (e.key === "F4") {
            e.preventDefault();
            window.print();
        }
    });
});
</script>
@endpush
