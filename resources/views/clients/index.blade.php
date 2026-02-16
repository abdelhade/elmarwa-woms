@extends('layouts.master')

@section('title', 'العملاء')
@section('page-title', 'العملاء')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-people me-2"></i>العملاء</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة (F4)
        </button>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel me-1"></i>استيراد (XLSX)
        </button>
        <a href="{{ route('clients.create') }}" class="btn btn-primary" id="btnAddNew"><i class="bi bi-plus-lg me-1"></i>إضافة عميل (F3)</a>
    </div>
</div>

@include('partials.alerts')

<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('clients.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control" placeholder="الاسم، البريد، أو الهاتف..." value="{{ request('search') }}">
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
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>حد الائتمان</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td>{{ $client->email ?? '—' }}</td>
                        <td>{{ $client->phone ?? '—' }}</td>
                        <td>{{ $client->credit_limit ? number_format((float)$client->credit_limit, 2) : '—' }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $client->name }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-people d-block"></i><p>لا يوجد عملاء بعد.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div class="card-footer d-flex justify-content-center">{{ $clients->links() }}</div>
    @endif
</div>



{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="importForm" action="{{ route('clients.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">استيراد عملاء من ملف Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">اختر ملف XLSX أو CSV</label>
                        <input type="file" name="file" class="form-control" id="importFile" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text mt-2">
                             يجب أن يحتوي الملف على الأعمدة التالية: <br>
                             <code>name</code> (إجباري), <code>email</code>, <code>phone</code>, <code>address</code>, <code>company</code>, <code>tax_number</code>, <code>credit_limit</code>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('clients.template') }}" class="btn btn-sm btn-link p-0 text-decoration-none">
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
