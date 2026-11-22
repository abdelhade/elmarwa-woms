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

{{-- Bulk Action Bar --}}
<div id="bulkBar" class="d-none mb-3">
    <div class="card border-primary shadow-sm">
        <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
            <span class="fw-semibold text-primary">
                <i class="bi bi-check2-square me-1"></i>
                تم تحديد <span id="selectedCount">0</span> عميل
            </span>
            <form id="bulkDeleteForm" action="{{ route('clients.bulkDestroy') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <div id="bulkIdsContainer"></div>
                <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>حذف المحدد
                </button>
            </form>
            <button type="button" id="btnClearSelection" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="bi bi-x-lg me-1"></i>إلغاء التحديد
            </button>
        </div>
    </div>
</div>

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
                    <th style="width: 40px;" class="text-center">
                        <input type="checkbox" id="selectAll" class="form-check-input" title="تحديد الكل">
                    </th>
                    <th style="width: 40px;"></th>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الترتيب</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>حد الائتمان</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="sortableClients">
                @forelse($clients as $client)
                    <tr data-id="{{ $client->id }}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $client->id }}">
                        </td>
                        <td class="drag-handle text-center" style="cursor: grab;"><i class="bi bi-arrows-move text-muted"></i></td>
                        <td>{{ $client->id }}</td>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td style="width: 100px;">
                            <input type="number" class="form-control form-control-sm text-center sort-input" 
                                   data-id="{{ $client->id }}" value="{{ $client->sort_order ?? '1' }}" min="1">
                        </td>
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
                    <tr><td colspan="9"><div class="empty-state"><i class="bi bi-people d-block"></i><p>لا يوجد عملاء بعد.</p></div></td></tr>
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    $('#importForm').on('submit', function() {
        $('#btnSubmitImport').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جاري الاستيراد...');
        $('#importStatus').removeClass('d-none');
    });

    // ── Bulk Selection ──
    function updateBulkBar() {
        let checked = $('.row-checkbox:checked');
        let count = checked.length;
        if (count > 0) {
            $('#selectedCount').text(count);
            $('#bulkBar').removeClass('d-none');
        } else {
            $('#bulkBar').addClass('d-none');
        }
        // Sync select-all state
        let total = $('.row-checkbox').length;
        $('#selectAll').prop('indeterminate', count > 0 && count < total);
        $('#selectAll').prop('checked', count === total && total > 0);
    }

    $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', this.checked);
        updateBulkBar();
    });

    $(document).on('change', '.row-checkbox', function() {
        updateBulkBar();
    });

    $('#btnClearSelection').on('click', function() {
        $('.row-checkbox, #selectAll').prop('checked', false);
        $('#selectAll').prop('indeterminate', false);
        $('#bulkBar').addClass('d-none');
    });

    $('#btnBulkDelete').on('click', function() {
        let checked = $('.row-checkbox:checked');
        if (checked.length === 0) return;

        if (!confirm(`هل أنت متأكد من حذف ${checked.length} عميل؟ لن يتم حذف العملاء المرتبطين بأوامر عمل.`)) return;

        // Build hidden inputs
        let container = $('#bulkIdsContainer');
        container.empty();
        checked.each(function() {
            container.append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
        });

        $('#bulkDeleteForm').submit();
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

    // Sort order inline update
    $('.sort-input').on('change', function() {
        let input = $(this);
        let id = input.data('id');
        let value = input.val();
        
        let originalBg = input.css('background-color');
        input.prop('disabled', true);
        
        $.ajax({
            url: `/clients/${id}/sort`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                sort_order: value
            },
            success: function(response) {
                if(response.success) {
                    input.css('background-color', '#d1e7dd'); // success color
                    setTimeout(() => input.css('background-color', originalBg), 1000);
                }
            },
            error: function() {
                input.css('background-color', '#f8d7da'); // error color
                setTimeout(() => input.css('background-color', originalBg), 1000);
                alert('حدث خطأ أثناء تحديث الترتيب');
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });

    // Drag and Drop ordering
    let sortableTbody = document.getElementById('sortableClients');
    if (sortableTbody) {
        new Sortable(sortableTbody, {
            handle: '.drag-handle',
            animation: 150,
            onStart: function (evt) {
                $(evt.item).css('background-color', '#fff3cd');
            },
            onEnd: function (evt) {
                $(evt.item).css('background-color', '');
                let order = [];
                $(sortableTbody).find('tr[data-id]').each(function() {
                    order.push($(this).data('id'));
                });

                let startIndex = {{ ($clients->currentPage() - 1) * $clients->perPage() + 1 }};

                $.ajax({
                    url: '{{ route("clients.updateSortBatch") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order,
                        start_index: startIndex
                    },
                    success: function(response) {
                        if(response.success) {
                            let currIdx = startIndex;
                            $(sortableTbody).find('tr[data-id]').each(function() {
                                let input = $(this).find('.sort-input');
                                input.val(currIdx);
                                let origBg = input.css('background-color');
                                input.css('background-color', '#d1e7dd');
                                setTimeout(() => input.css('background-color', origBg), 800);
                                currIdx++;
                            });
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء حفظ الترتيب الجديد');
                    }
                });
            }
        });
    }
});
</script>
@endpush
