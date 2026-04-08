@extends('layouts.master')

@section('title', 'المنتجات')
@section('page-title', 'المنتجات')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-box-seam me-2"></i>المنتجات</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة (F4)
        </button>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i>استيراد (XLSX)
        </button>
        <a href="{{ route('products.create') }}" class="btn btn-primary" id="btnAddNew">
            <i class="bi bi-plus-lg me-1"></i>إضافة منتج (F3)
        </a>
    </div>
</div>

@include('partials.alerts')

<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('products.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control" placeholder="الاسم أو SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">التصنيف</label>
                    <select name="category_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">وحدة القياس</label>
                    <select name="uom" class="form-select">
                        <option value="">الكل</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->value }}" {{ request('uom') == $unit->value ? 'selected' : '' }}>{{ $unit->label() }}</option>
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

<div class="card data-table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>الاسم</th>
                    <th>التصنيف</th>
                    <th>وحدة القياس</th>
                    <th>السعر</th>
                    <th>نسبة الهالك</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td><code>{{ $product->sku }}</code></td>
                        <td class="fw-semibold">{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '—' }}</td>
                        <td>{{ $product->uom?->label() ?? '—' }}</td>
                        <td>{{ number_format((float)$product->unit_price, 2) }}</td>
                        <td>{{ $product->waste_rate }}%</td>
                        <td class="actions-cell">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $product->name }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-box-seam d-block"></i><p>لا توجد منتجات بعد.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div class="card-footer d-flex justify-content-center">{{ $products->links() }}</div>
    @endif
</div>



{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importForm" action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">استيراد منتجات من ملف Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">اختر ملف XLSX أو CSV</label>
                        <input type="file" name="file" class="form-control" id="importFile" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text mt-2">
                             يجب أن يحتوي الملف على الأعمدة التالية: <br>
                             <code>category_id</code> (إجباري), <code>name</code> (إجباري), <code>sku</code>, <code>uom</code>, <code>lead_time_days</code>, <code>waste_rate</code>, <code>unit_weight</code>, <code>unit_price</code>, <code>description</code>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('products.template') }}" class="btn btn-sm btn-link p-0 text-decoration-none">
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
        if (e.key === 'F3') {
            e.preventDefault();
            document.getElementById('btnAddNew').click();
        }
        if (e.key === 'F4') {
            e.preventDefault();
            window.print();
        }
    });
});
</script>
@endpush
