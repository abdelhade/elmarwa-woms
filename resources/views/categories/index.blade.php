@extends('layouts.master')

@section('title', 'التصنيفات')
@section('page-title', 'التصنيفات')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <h4><i class="bi bi-diagram-3 me-2"></i>التصنيفات</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة (F4)
        </button>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i>استيراد (XLSX)
        </button>
        <a href="{{ route('categories.create') }}" class="btn btn-primary" id="btnAddNew">
            <i class="bi bi-plus-lg me-1"></i>إضافة تصنيف (F3)
        </a>
    </div>
</div>

{{-- Alerts --}}
@include('partials.alerts')

{{-- Filters --}}
<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('categories.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">بحث بالاسم</label>
                    <input type="text" name="search" class="form-control" placeholder="ابحث..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">التصنيف الأب</label>
                    <select name="parent_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
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
                    <th>التصنيف الأب</th>
                    <th>التصنيفات الفرعية</th>
                    <th>المنتجات</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td>{{ $category->parent->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $category->children_count }}</span></td>
                        <td><span class="badge bg-info">{{ $category->products_count }}</span></td>
                        <td class="actions-cell">
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $category->name }}" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-diagram-3 d-block"></i>
                                <p>لا توجد تصنيفات بعد.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        <div class="card-footer d-flex justify-content-center">
            {{ $categories->links() }}
        </div>
    @endif
</div>



{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importForm" action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">استيراد تصنيفات من ملف Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">اختر ملف XLSX أو CSV</label>
                        <input type="file" name="file" class="form-control" id="importFile" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text mt-2">
                             يجب أن يحتوي الملف على الأعمدة التالية: <br>
                             <code>name</code> (إجباري), <code>parent_id</code>, <code>description</code>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('categories.template') }}" class="btn btn-sm btn-link p-0 text-decoration-none">
                                <i class="bi bi-download me-1"></i>تحميل نموذج التجربة (CSV)
                            </a>
                        </div>
                    </div>
                    <div id="importStatus" class="d-none">
                        <div class="d-flex align-items-center text-primary">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <span>جاري معالجة الملف، برجاء الانتظار...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitImport">بدء الاستيراد</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#importForm').on('submit', function() {
        $('#btnSubmitImport').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جاري الاستيراد...');
        $('#importStatus').removeClass('d-none');
    });

    // اختصار F3 للإضافة الجديدة
    $(document).on('keydown', function(e) {
        if (e.key === "F3") {
            e.preventDefault();
            const btn = document.getElementById('btnAddNew');
            if (btn) btn.click();
        }
        if (e.key === "F4") {
            e.preventDefault();
            window.print();
        }
    });
});
</script>
@endpush
