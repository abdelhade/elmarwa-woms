@extends('layouts.master')

@section('title', 'إضافة أمر عمل')
@section('page-title', 'إضافة أمر عمل')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Select2 Bootstrap 5 Theme overrides */
    .select2-container .select2-selection--single {
        height: 31px;
        padding: 0.15rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
        padding-right: 0;
    }
    /* ── Fixed Width Container ── */
    .wo-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* ── Autocomplete Dropdown ── */
    .wo-ac-wrap { position: relative; width: 100%; }
    .wo-ac-wrap .input-group-text {
        background: transparent;
        border-right: none;
        color: var(--bs-secondary);
    }
    .wo-ac-wrap .form-control-sm {
        border-left: none;
        font-size: 0.9rem;
    }
    .wo-ac-wrap .wo-clear-search {
        position: absolute;
        left: 35px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        cursor: pointer;
        display: none;
        color: var(--bs-secondary);
        padding: 5px;
    }
    .wo-ac-wrap .wo-clear-search:hover { color: var(--bs-danger); }

    .wo-client-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 9999 !important;
        background: #fff;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.5rem;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .wo-ac-dropdown {
        display: none;
        position: absolute;
        bottom: 100%; /* Appear above */
        left: 0;
        right: 0;
        z-index: 9999 !important;
        background: #fff;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.5rem;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0 -10px 25px rgba(0,0,0,0.2); /* Shadow upwards */
        margin-bottom: 5px;
    }
    .wo-ac-dropdown.show, .wo-client-dropdown.show { display: block; animation: woFadeIn 0.2s ease; }
    @keyframes woFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .wo-ac-dropdown table { margin-bottom: 0; font-size: 0.85rem; width: 100%; }
    .wo-ac-dropdown thead { position: sticky; top: 0; z-index: 2; background: var(--bs-light); }
    .wo-ac-dropdown thead th { border-top: none; padding: 10px; font-weight: 600; color: var(--bs-secondary); text-transform: uppercase; font-size: 0.75rem; }
    .wo-ac-dropdown tbody tr { cursor: pointer; transition: all 0.15s; }
    .wo-ac-dropdown tbody td { padding: 10px; vertical-align: middle; border-bottom-color: rgba(var(--bs-border-color-rgb), 0.5); }
    
    .wo-ac-dropdown tbody tr:hover,
    .wo-ac-dropdown tbody tr.wo-ac-active,
    .wo-client-dropdown .list-group-item:hover,
    .wo-client-dropdown .list-group-item.active {
        background: #ffed4a !important;
        color: #000 !important;
    }
    .wo-ac-dropdown tbody tr:hover *,
    .wo-ac-dropdown tbody tr.wo-ac-active *,
    .wo-client-dropdown .list-group-item:hover,
    .wo-client-dropdown .list-group-item.active {
        color: #000 !important;
    }
    
    .wo-ac-noresult {
        text-align: center;
        padding: 2rem;
        color: var(--bs-secondary);
    }

    .wo-details-table { width: 100%; table-layout: fixed; }
    .wo-details-table .qty-input { width: 80px; text-align: center; height: 31px; }
    .wo-details-table .price-input { width: 100px; text-align: center; height: 31px; }
    .wo-details-empty { color: var(--bs-secondary); text-align: center; padding: 1rem; }
    .wo-details-empty i { font-size: 1.5rem; display: block; margin-bottom: .25rem; }

    .form-card { margin-bottom: 0.75rem !important; }
    .form-card .card-body { padding: 0.75rem !important; }
    .wo-section-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: .4rem;
        color: var(--bs-primary);
    }
    
    /* ── Fixed Width Form Fields ── */
    .wo-form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    .wo-form-col {
        flex: 1;
        min-width: 0;
    }
    .wo-form-col label {
        font-size: 0.85rem;
        margin-bottom: 5px;
        display: block;
    }

    /* ── Table Compactness ── */
    .table-sm-custom td, .table-sm-custom th { padding: 6px 8px !important; font-size: 0.88rem; }
    .page-header { margin-bottom: 0.75rem !important; padding-bottom: 0.5rem !important; }

    /* ── Grand Total ── */
    .wo-grand-total { font-size: 1.1rem; font-weight: 700; }
    
    /* ── Shortcuts Modal ── */
    .wo-shortcut-row { display: flex; justify-content: space-between; align-items: center; padding: .4rem 0; border-bottom: 1px solid var(--bs-border-color); }
    .wo-shortcut-row:last-child { border-bottom: none; }
    .wo-shortcut-row kbd {
        background: var(--bs-dark);
        color: var(--bs-light);
        padding: 3px 10px;
        border-radius: 5px;
        font-size: .85rem;
        min-width: 50px;
        text-align: center;
    }

    /* ── Attributes Styling ── */
    .wo-attr-badge {
        display: inline-flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 2px 6px;
        margin: 2px;
        font-size: 0.75rem;
    }
    .wo-attr-badge .remove {
        cursor: pointer;
        margin-right: 4px;
        color: #dc3545;
    }
    .wo-attr-input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.8rem;
        width: 100px;
    }
    .wo-attr-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        min-height: 31px;
        padding: 2px 5px;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .wo-attr-wrap:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .table-responsive { overflow: visible !important; }
</style>
@endpush

@section('content')

<div class="wo-container">
    <div class="page-header">
        <h4><i class="bi bi-plus-lg me-2"></i>إضافة أمر عمل جديد</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#woShortcutsModal" title="اختصارات لوحة المفاتيح">
                <i class="bi bi-keyboard me-1"></i>اختصارات
            </button>
            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
        </div>
    </div>

    @include('partials.alerts')

    <form method="POST" action="{{ route('work-orders.store') }}" id="woCreateForm">
        @csrf

        {{-- ════════ Section 1: Order Info ════════ --}}
        <div class="card form-card shadow-sm border-0">
            <div class="card-body">
                <div class="wo-section-title"><i class="bi bi-info-circle"></i> بيانات أمر العمل الأساسية</div>
                <div class="wo-form-row">
                    <div class="wo-form-col">
                        <label for="client_search">العميل <span class="text-danger">*</span></label>
                        <div class="position-relative" id="clientSearchWrap">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" id="clientSearchInput" class="form-control form-control-sm border-start-0" placeholder="ابحث عن عميل..." autocomplete="off">
                                <input type="hidden" name="client_id" id="client_id_hidden" value="{{ old('client_id') }}">
                            </div>
                            <div class="wo-client-dropdown" id="woClientDropdown">
                                <div class="list-group list-group-flush" id="woClientList"></div>
                                <div id="woClientNoResult" class="p-3 text-center text-muted" style="display:none;">لا يوجد عملاء بهذا الاسم</div>
                            </div>
                        </div>
                        @error('client_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="wo-form-col">
                        <label for="order_batch_id">الدفعة (اختياري)</label>
                        <select name="order_batch_id" id="order_batch_id" class="form-select form-select-sm @error('order_batch_id') is-invalid @enderror">
                            <option value="">— بدون دفعة —</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ old('order_batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }} ({{ $batch->batch_number }})</option>
                            @endforeach
                        </select>
                        @error('order_batch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="wo-form-col">
                        <label for="due_date">تاريخ التسليم</label>
                        <input type="date" name="due_date" id="due_date" class="form-control form-control-sm @error('due_date') is-invalid @enderror" value="{{ old('due_date') ?? date('Y-m-d') }}">
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                
                <div class="wo-form-row mt-3">
                    <div class="wo-form-col">
                        <label for="employee_id">الموظف</label>
                        <select name="employee_id" id="employee_id" class="form-select form-select-sm @error('employee_id') is-invalid @enderror">
                            <option value="">— اختر الموظف —</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="wo-form-col">
                        <label for="operator_id">المشغل</label>
                        <select name="operator_id" id="operator_id" class="form-select form-select-sm @error('operator_id') is-invalid @enderror">
                            <option value="">— اختر المشغل —</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}" {{ old('operator_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('operator_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="wo-form-col">
                        <label for="supervisor_id">مسؤول التشغيل</label>
                        <select name="supervisor_id" id="supervisor_id" class="form-select form-select-sm @error('supervisor_id') is-invalid @enderror">
                            <option value="">— اختر مسؤول التشغيل —</option>
                            @foreach($employees ?? [] as $employee)
                                <option value="{{ $employee->id }}" {{ old('supervisor_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('supervisor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ════════ Section 2: Order Details ════════ --}}
        <div class="card form-card shadow-sm border-0">
            <div class="card-body">
                <div class="wo-section-title mb-2"><i class="bi bi-box-seam"></i> أصناف أمر العمل</div>

                <div class="table-responsive" style="overflow: visible;">
                    <table class="table table-bordered table-sm-custom wo-details-table mb-0" id="woDetailsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:20%">التصنيف</th>
                                <th style="width:20%">خصائص التصنيف</th>
                                <th style="width:20%">الصنف</th>
                                <th style="width:25%">خصائص الصنف</th>
                                <th style="width:10%">الكمية</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody id="woDetailsBody">
                            {{-- Rows added dynamically --}}
                        </tbody>
                    </table>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="woAddRowBtn">
                            <i class="bi bi-plus-lg me-1"></i>إضافة صف (F3)
                        </button>
                    </div>
                </div>

                <div class="wo-form-row mt-3">
                    <div class="wo-form-col">
                        <label for="notes" class="form-label">ملاحظات إضافية (اختياري)</label>
                        <textarea name="notes" id="notes" rows="2" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="اكتب الملاحظات هنا...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4" id="woSaveBtn">
                        <i class="bi bi-check-lg me-1"></i>حفظ أمر العمل (F12)
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function () {
    'use strict';

    // Data from Server
    const _products = {!! json_encode($products->map(function($p) {
        return ['id' => $p->id, 'name' => $p->name, 'category_id' => $p->category_id];
    })->values()) !!};
    
    const _categories = {!! json_encode($categories->map(function($c) {
        return ['id' => $c->id, 'name' => $c->name];
    })->values()) !!};

    const _catsAttrsList = {!! json_encode($allCategoryAttributes->pluck('name')) !!};
    const _itemAttrsList = {!! json_encode($allAttributes->pluck('name')) !!};

    // DOM Elements
    const $detBody = $('#woDetailsBody');
    const $form = $('#woCreateForm');

    // Excel-like State
    let _rowCount = 0;

    // Build Row HTML
    function buildRowHTML(idx) {
        let catOptions = '<option value="">— اختر التصنيف —</option>';
        _categories.forEach(c => { catOptions += `<option value="${c.id}">${c.name}</option>`; });
        
        return `
        <tr data-idx="${idx}" class="wo-item-row">
            <td>
                <select class="form-select form-select-sm wo-cat-select" data-idx="${idx}" required>
                    ${catOptions}
                </select>
            </td>
            <td>
                <div class="wo-attr-wrap" data-idx="${idx}" data-type="cat">
                    <div class="wo-attr-list d-flex flex-wrap"></div>
                    <input type="text" class="wo-attr-input" placeholder="خاصية تصنيف.." data-idx="${idx}" data-type="cat">
                </div>
                <div class="wo-hid-cat-attrs" data-idx="${idx}"></div>
            </td>
            <td>
                <select name="details[${idx}][product_id]" class="form-select form-select-sm wo-prod-select" data-idx="${idx}" required disabled>
                    <option value="">— اختر التصنيف أولاً —</option>
                </select>
            </td>
            <td>
                <div class="wo-attr-wrap" data-idx="${idx}" data-type="item">
                    <div class="wo-attr-list d-flex flex-wrap"></div>
                    <input type="text" class="wo-attr-input" placeholder="خاصية صنف.." data-idx="${idx}" data-type="item">
                </div>
                <div class="wo-hid-item-attrs" data-idx="${idx}"></div>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="details[${idx}][quantity]" class="form-control form-control-sm wo-qty-input" data-idx="${idx}" value="1" required>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" data-idx="${idx}" title="حذف">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    }

    // Add Initial Row on Load
    function addRow() {
        const html = buildRowHTML(_rowCount);
        $detBody.append(html);
        
        // Add Datalists to the new inputs
        addDatalist($detBody.find(`.wo-attr-input[data-idx="${_rowCount}"][data-type="cat"]`), _catsAttrsList);
        addDatalist($detBody.find(`.wo-attr-input[data-idx="${_rowCount}"][data-type="item"]`), _itemAttrsList);
        
        // Initialize Select2
        $detBody.find(`.wo-cat-select[data-idx="${_rowCount}"]`).select2({ width: '100%' });
        $detBody.find(`.wo-prod-select[data-idx="${_rowCount}"]`).select2({ width: '100%' });
        
        _rowCount++;
    }

    $('#woAddRowBtn').on('click', function() { addRow(); });

    // Category Change -> Filter Products
    $detBody.on('change', '.wo-cat-select', function() {
        const idx = $(this).data('idx');
        const catId = $(this).val();
        const $prodSelect = $detBody.find(`.wo-prod-select[data-idx="${idx}"]`);
        
        $prodSelect.empty();
        if (!catId) {
            $prodSelect.append('<option value="">— اختر التصنيف أولاً —</option>');
            $prodSelect.prop('disabled', true);
            return;
        }

        const filtered = _products.filter(p => String(p.category_id) === String(catId));
        $prodSelect.append('<option value="">— اختر الصنف —</option>');
        filtered.forEach(p => { $prodSelect.append(`<option value="${p.id}">${p.name}</option>`); });
        $prodSelect.prop('disabled', false).trigger('change.select2');
    });

    // Remove Row
    $detBody.on('click', '.btn-remove-row', function() {
        if ($detBody.find('tr').length <= 1) {
            alert('يجب أن يحتوي أمر العمل على صنف واحد على الأقل.');
            return;
        }
        $(this).closest('tr').remove();
    });

    // ── Attributes Logic ──
    const _rowAttrs = {}; 
    // _rowAttrs[idx] = { cat: [], item: [] }

    function renderAttributes(idx, type) {
        if (!_rowAttrs[idx]) _rowAttrs[idx] = { cat: [], item: [] };
        const arr = _rowAttrs[idx][type];
        const $row = $detBody.find(`tr[data-idx="${idx}"]`);
        const $list = $row.find(`.wo-attr-wrap[data-type="${type}"] .wo-attr-list`);
        const $hiddens = $row.find(type === 'cat' ? '.wo-hid-cat-attrs' : '.wo-hid-item-attrs');
        
        $list.empty();
        $hiddens.empty();
        
        const typeKey = type === 'cat' ? 'category_attributes' : 'attributes';

        arr.forEach((attr, aIdx) => {
            const name = attr.name || '';
            const value = attr.value || '';
            const disp = name + (value ? ': ' + value : '');
            
            $list.append(`
                <div class="wo-attr-badge">
                    <i class="bi bi-trash remove-attr" data-idx="${idx}" data-type="${type}" data-aidx="${aIdx}"></i>
                    ${disp.replace(/</g, "&lt;")}
                </div>
            `);
            
            $hiddens.append(`<input type="hidden" name="details[${idx}][${typeKey}][${aIdx}][name]" value="${name.replace(/"/g, '&quot;')}">`);
            $hiddens.append(`<input type="hidden" name="details[${idx}][${typeKey}][${aIdx}][value]" value="${value.replace(/"/g, '&quot;')}">`);
        });
    }

    $detBody.on('keydown', '.wo-attr-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = $(this).val().trim();
            if (!val) return;
            
            const idx = $(this).data('idx');
            const type = $(this).data('type');
            const parts = val.split(':');
            const name = parts[0].trim();
            const value = parts.length > 1 ? parts.slice(1).join(':').trim() : '';
            
            if (name) {
                if (!_rowAttrs[idx]) _rowAttrs[idx] = { cat: [], item: [] };
                _rowAttrs[idx][type].push({ name, value });
                $(this).val('');
                renderAttributes(idx, type);
            }
        }
    });

    $detBody.on('click', '.remove-attr', function() {
        const idx = $(this).data('idx');
        const type = $(this).data('type');
        const aidx = $(this).data('aidx');
        _rowAttrs[idx][type].splice(aidx, 1);
        renderAttributes(idx, type);
    });

    function addDatalist($el, list) {
        const rId = 'dl_' + Math.random().toString(36).substr(2, 9);
        $el.attr('list', rId);
        let $dl = $(`<datalist id="${rId}"></datalist>`);
        list.forEach(item => { $dl.append(`<option value="${item}: ">`); });
        $el.after($dl);
    }

    // ── Keyboard Shortcuts ──
    $(document).on('keydown', function(e) {
        if (e.key === 'F3') { e.preventDefault(); addRow(); }
        if (e.key === 'F12') { 
            e.preventDefault(); 
            if ($detBody.find('tr').length) $form.submit(); 
        }
    });

    // Handle Client Search Autocomplete (Legacy logic)
    const _clients = {!! json_encode($clients->map(fn($c) => ['id'=>$c->id,'name'=>$c->name])) !!};
    const $cSearch = $('#clientSearchInput');
    const $cHidden = $('#client_id_hidden');
    const $cDropdown = $('#woClientDropdown');
    const $cList = $('#woClientList');

    $cSearch.on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        $cHidden.val('');
        if (!q) { $cDropdown.removeClass('show'); return; }
        
        const res = _clients.filter(c => c.name.toLowerCase().includes(q));
        $cList.empty();
        if(!res.length) { $cDropdown.removeClass('show'); return; }
        
        res.forEach((c, i) => { $cList.append(`<a href="#" class="list-group-item list-group-item-action py-2" data-idx="${i}" data-id="${c.id}">${c.name}</a>`); });
        $cDropdown.addClass('show');
    });

    $cList.on('click', '.list-group-item', function(e) {
        e.preventDefault();
        $cSearch.val($(this).text());
        $cHidden.val($(this).data('id'));
        $cDropdown.removeClass('show');
    });

    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#clientSearchWrap').length) $cDropdown.removeClass('show');
    });

    // Form Validation simple
    $form.on('submit', function(e) {
        if (!$cHidden.val()) {
            e.preventDefault();
            alert('يجب اختيار العميل!');
            $cSearch.focus();
        }
    });

    // Init first row!
    addRow();

})();
</script>
@endpush
