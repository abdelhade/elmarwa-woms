@extends('layouts.master')

@section('title', 'إضافة أمر عمل')
@section('page-title', 'إضافة أمر عمل')

@push('styles')
<style>
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

    .wo-ac-dropdown, .wo-client-dropdown {
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
    .wo-ac-dropdown.show, .wo-client-dropdown.show { display: block; animation: woFadeIn 0.2s ease; }
    @keyframes woFadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

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
            </div>
        </div>

        {{-- ════════ Section 2: Order Details ════════ --}}
        <div class="card form-card shadow-sm border-0">
            <div class="card-body">
                <div class="wo-section-title mb-2"><i class="bi bi-box-seam"></i> أصناف أمر العمل</div>

                {{-- Selected Details Table --}}
                <div style="overflow-x: auto;">
                    <table class="table table-bordered table-sm-custom wo-details-table mb-0" id="woDetailsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:30%">الصنف</th>
                                <th>المجموعة</th>
                                <th style="width:20%">الخصائص</th>
                                <th style="width:10%">الكمية</th>
                                <th style="width:10%">سعر الوحدة</th>
                                <th style="width:10%">الإجمالي</th>
                                <th style="width:4%"></th>
                            </tr>
                        </thead>
                        <tbody id="woDetailsBody">
                            {{-- Rows added dynamically --}}
                        </tbody>
                        <tbody>
                            {{-- Inline Search Row --}}
                            <tr>
                                <td class="text-center align-middle bg-light"><i class="bi bi-search text-secondary small"></i></td>
                                <td colspan="2" class="p-0 position-relative">
                                    <div class="wo-ac-wrap w-100">
                                        <input type="text" id="woProductSearch" class="form-control form-control-sm border-0 py-2 px-3" 
                                               style="background: none; outline: none; box-shadow: none;"
                                               placeholder="ابحث هنا لإضافة صنف جديد... (F2)" autocomplete="off">
                                        
                                        <div class="wo-ac-dropdown" id="woAcDropdown" style="width: 100%; top: 100%;">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th style="width:50%">الصنف</th>
                                                        <th>الكود</th>
                                                        <th>المجموعة</th>
                                                        <th class="text-end">السعر</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="woAcBody"></tbody>
                                            </table>
                                            <div class="wo-ac-noresult" id="woAcNoResult" style="display:none;">
                                                <div class="p-3 text-center text-muted">لا توجد نتائج</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="3" class="align-middle text-muted small px-3 bg-light">
                                    <i class="bi bi-keyboard me-1"></i> ابحث عن الصنف ثم اضغط <kbd class="bg-secondary opacity-75">Enter</kbd>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="woDetailsFoot" style="display:none;">
                            <tr class="table-light">
                                <td colspan="6" class="text-start fw-bold py-2">الإجمالي الكلي</td>
                                <td class="wo-grand-total py-2" id="woGrandTotal">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="wo-details-empty" id="woDetailsEmpty">
                        <i class="bi bi-inbox"></i>
                        لم يتم إضافة أصناف بعد.<br>
                        <small>ابحث عن صنف بالأعلى واضغط <kbd>Enter</kbd> لإضافته</small>
                    </div>
                </div>

                <div class="wo-form-row mt-2">
                    <div class="wo-form-col">
                        <textarea name="notes" id="notes" rows="1" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3" id="woSaveBtn">
                        <i class="bi bi-check-lg me-1"></i>حفظ (F12)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger px-3" id="woClearAll">
                        <i class="bi bi-trash3 me-1"></i>مسح
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ════════ Shortcuts Modal ════════ --}}
<div class="modal fade" id="woShortcutsModal" tabindex="-1" aria-labelledby="woShortcutsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="woShortcutsLabel"><i class="bi bi-keyboard me-2"></i>اختصارات لوحة المفاتيح</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="wo-shortcut-row">
                    <span>التركيز على البحث</span>
                    <kbd>F2</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>التنقل بين نتائج البحث</span>
                    <div><kbd>↑</kbd> <kbd>↓</kbd></div>
                </div>
                <div class="wo-shortcut-row">
                    <span>إضافة الصنف المحدد</span>
                    <kbd>Enter</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>إغلاق قائمة البحث</span>
                    <kbd>Esc</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>حذف آخر صنف مضاف</span>
                    <kbd>Delete</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>التنقل بين خانات الكمية</span>
                    <kbd>Tab</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>حفظ أمر العمل</span>
                    <kbd>Ctrl+S</kbd>
                </div>
                <div class="wo-shortcut-row">
                    <span>فتح هذه النافذة</span>
                    <kbd>F1</kbd>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Products data (from server, kept in JS array for fast client-side search) ──
    var _woProducts = {!! json_encode($products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?? '',
            'price' => (float) $p->unit_price,
            'category' => $p->category->name ?? '—',
        ];
    })->values()) !!};

    var _woClients = {!! json_encode($clients->map(function($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
        ];
    })) !!};

    var _allAttributes = {!! json_encode($allAttributes->map(function($a) {
        return [
            'id' => $a->id,
            'name' => $a->name,
            'type' => $a->type,
            'options' => $a->options,
        ];
    })) !!};

    // ── State ──
    var _woDetails = [];
    var _woAcIdx = -1;
    var _woAcVisible = [];
    var _woClientIdx = -1;
    var _woClientVisible = [];

    // ── DOM Refs ──
    var $search    = $('#woProductSearch');
    var $dropdown  = $('#woAcDropdown');
    var $acBody    = $('#woAcBody');
    var $acNoRes   = $('#woAcNoResult');

    var $cSearch   = $('#clientSearchInput');
    var $cHidden   = $('#client_id_hidden');
    var $cDropdown = $('#woClientDropdown');
    var $cList     = $('#woClientList');
    var $cNoRes    = $('#woClientNoResult');

    var $detBody   = $('#woDetailsBody');
    var $detEmpty  = $('#woDetailsEmpty');
    var $detFoot   = $('#woDetailsFoot');
    var $grandTot  = $('#woGrandTotal');
    var $form      = $('#woCreateForm');

    // ══════════════════════════════════════
    //  AUTOCOMPLETE: Search & Show Dropdown
    // ══════════════════════════════════════
    $search.on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        _woAcIdx = -1;

        if (!q) {
            _woHideDropdown();
            return;
        }

        // Filter products
        _woAcVisible = [];
        for (var i = 0; i < _woProducts.length; i++) {
            var p = _woProducts[i];
            var nameMatch = p.name.toLowerCase().indexOf(q) > -1;
            var skuMatch  = p.sku.toLowerCase().indexOf(q) > -1;
            if (nameMatch || skuMatch) {
                _woAcVisible.push(p);
            }
        }

        _woRenderDropdown(q);
    });

    $search.on('focus', function () {
        var q = $(this).val().trim();
        if (q && _woAcVisible.length) {
            $dropdown.addClass('show');
        }
    });

    function _woHideDropdown() {
        $dropdown.removeClass('show');
        _woAcIdx = -1;
    }

    // ══════════════════════════════════════
    //  CLIENT SEARCH LOGIC
    // ══════════════════════════════════════
    $cSearch.on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        _woClientIdx = -1;
        $cHidden.val('');

        if (!q) {
            $cDropdown.removeClass('show');
            return;
        }

        _woClientVisible = _woClients.filter(c => c.name.toLowerCase().includes(q));
        _woRenderClientDropdown();
    });

    $cSearch.on('focus', function () {
        if ($(this).val().trim() && _woClientVisible.length) {
            $cDropdown.addClass('show');
        }
    });

    function _woRenderClientDropdown() {
        $cList.empty();
        if (!_woClientVisible.length) {
            $cNoRes.show();
            $cDropdown.addClass('show');
            return;
        }
        $cNoRes.hide();
        _woClientVisible.forEach((c, i) => {
            $cList.append(`<a href="#" class="list-group-item list-group-item-action py-2" data-idx="${i}">${_woEsc(c.name)}</a>`);
        });
        _woClientIdx = 0;
        $cList.find('.list-group-item').first().addClass('active');
        $cDropdown.addClass('show');
    }

    $cList.on('click', '.list-group-item', function (e) {
        e.preventDefault();
        var idx = $(this).data('idx');
        _woSelectClient(_woClientVisible[idx]);
    });

    function _woSelectClient(client) {
        $cSearch.val(client.name);
        $cHidden.val(client.id);
        $cDropdown.removeClass('show');
        $search.focus();
    }

    $cSearch.on('keydown', function (e) {
        if (!$cDropdown.hasClass('show')) return;
        var $items = $cList.find('.list-group-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            _woClientIdx = Math.min(_woClientIdx + 1, $items.length - 1);
            _woHighlightClient($items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            _woClientIdx = Math.max(_woClientIdx - 1, 0);
            _woHighlightClient($items);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (_woClientIdx >= 0) _woSelectClient(_woClientVisible[_woClientIdx]);
        } else if (e.key === 'Escape') {
            $cDropdown.removeClass('show');
        }
    });

    function _woHighlightClient($items) {
        $items.removeClass('active');
        var $target = $items.eq(_woClientIdx);
        $target.addClass('active');
        $target[0].scrollIntoView({ block: 'nearest' });
    }

    $(document).on('mousedown', function (e) {
        if (!$(e.target).closest('#clientSearchWrap').length) $cDropdown.removeClass('show');
        if (!$(e.target).closest('.wo-ac-wrap').length) _woHideDropdown();
    });

    function _woRenderDropdown(query) {
        $acBody.empty();

        if (!_woAcVisible.length) {
            $acNoRes.show();
            $dropdown.addClass('show');
            return;
        }

        $acNoRes.hide();

        for (var i = 0; i < _woAcVisible.length; i++) {
            var p = _woAcVisible[i];
            // Check if already added
            var added = _woIsAdded(p.id);
            var cls = added ? ' class="text-muted opacity-75"' : '';
            var badge = added ? ' <span class="badge bg-success bg-opacity-10 text-success ms-1" style="font-size:0.65rem">مضاف مسبقاً</span>' : '';

            var displayName = _woEsc(p.name);
            var displaySku  = _woEsc(p.sku);

            var row = '<tr data-acid="' + i + '"' + cls + '>' +
                '<td>' +
                    '<div class="d-flex align-items-center">' +
                        '<div class="flex-grow-1">' +
                            '<div class="fw-bold text-dark mb-0">' + displayName + badge + '</div>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td><code class="text-primary">' + displaySku + '</code></td>' +
                '<td class="text-muted">' + _woEsc(p.category) + '</td>' +
                '<td class="text-end fw-bold text-primary">' + p.price.toLocaleString(undefined, {minimumFractionDigits: 2}) + '</td>' +
                '</tr>';
            $acBody.append(row);
        }

        // Auto-highlight first
        $acBody.find('tr').first().addClass('wo-ac-active');
        _woAcIdx = 0;

        $dropdown.addClass('show');
    }

    function _woHideDropdown() {
        $dropdown.removeClass('show');
        _woAcIdx = -1;
    }

    function _woIsAdded(pid) {
        for (var i = 0; i < _woDetails.length; i++) {
            if (_woDetails[i].pid === pid) return true;
        }
        return false;
    }

    // ══════════════════════════════════════
    //  AUTOCOMPLETE: Click to add
    // ══════════════════════════════════════
    $acBody.on('click', 'tr', function () {
        var idx = parseInt($(this).data('acid'));
        _woAddProduct(_woAcVisible[idx]);
    });

    // ══════════════════════════════════════
    //  AUTOCOMPLETE: Keyboard navigation
    // ══════════════════════════════════════
    $search.on('keydown', function (e) {
        if (!$dropdown.hasClass('show')) {
            // If typing Enter with no dropdown, do nothing (don't submit form)
            if (e.key === 'Enter') e.preventDefault();
            return;
        }

        var $rows = $acBody.find('tr');
        if (!$rows.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            _woAcIdx = Math.min(_woAcIdx + 1, $rows.length - 1);
            _woHighlightAc($rows);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            _woAcIdx = Math.max(_woAcIdx - 1, 0);
            _woHighlightAc($rows);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (_woAcIdx >= 0 && _woAcIdx < _woAcVisible.length) {
                _woAddProduct(_woAcVisible[_woAcIdx]);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            _woHideDropdown();
        }
    });

    function _woHighlightAc($rows) {
        $rows.removeClass('wo-ac-active');
        var $target = $rows.eq(_woAcIdx);
        $target.addClass('wo-ac-active');
        // Scroll into view
        var dd = $dropdown[0];
        var row = $target[0];
        if (row && dd) {
            var rowTop = row.offsetTop;
            var rowH   = row.offsetHeight;
            var ddScroll = dd.scrollTop;
            var ddH     = dd.clientHeight;
            if (rowTop < ddScroll) dd.scrollTop = rowTop;
            else if (rowTop + rowH > ddScroll + ddH) dd.scrollTop = rowTop + rowH - ddH;
        }
    }

    // ══════════════════════════════════════
    //  ADD PRODUCT TO DETAILS
    // ══════════════════════════════════════
    function _woAddProduct(product) {
        if (!product) return;

        // Check duplicate → focus existing qty
        for (var i = 0; i < _woDetails.length; i++) {
            if (_woDetails[i].pid === product.id) {
                $('#woQty_' + product.id).select().focus();
                $search.val('');
                _woHideDropdown();
                return;
            }
        }

        _woDetails.push({
            pid:      product.id,
            name:     product.name,
            sku:      product.sku || '',
            category: product.category || '—',
            qty:      1,
            price:    product.price || 0,
            attributes: []
        });

        _woRenderDetails();

        // Clear search, hide dropdown, then focus qty
        $search.val('');
        _woHideDropdown();
        setTimeout(function () { $('#woQty_' + product.id).select().focus(); }, 50);
    }

    // ══════════════════════════════════════
    //  RENDER DETAILS TABLE
    // ══════════════════════════════════════
    function _woRenderDetails() {
        $detBody.empty();

        if (!_woDetails.length) {
            $detEmpty.show();
            $detFoot.hide();
            $grandTot.text('0.00');
            return;
        }

        $detEmpty.hide();
        $detFoot.show();
        var grand = 0;

        for (var i = 0; i < _woDetails.length; i++) {
            var d = _woDetails[i];
            var lineTotal = (d.qty * d.price).toFixed(2);
            grand += parseFloat(lineTotal);

            var html = '<tr data-idx="' + i + '">' +
                '<td>' + (i + 1) + '</td>' +
                '<td><strong>' + _woEsc(d.name) + '</strong> <small class="text-muted d-block">' + _woEsc(d.sku) + '</small></td>' +
                '<td class="text-muted">' + _woEsc(d.category) + '</td>' +
                '<td>' +
                    '<div class="wo-attr-wrap" data-idx="' + i + '">' +
                        '<div class="wo-attr-list d-flex flex-wrap"></div>' +
                        '<input type="text" class="wo-attr-input" placeholder="أضف خاصية..." data-idx="' + i + '">' +
                    '</div>' +
                '</td>' +
                '<td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm qty-input wo-detail-qty" ' +
                    'id="woQty_' + d.pid + '" value="' + d.qty + '" data-idx="' + i + '"></td>' +
                '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm price-input wo-detail-price" ' +
                    'value="' + d.price.toFixed(2) + '" data-idx="' + i + '"></td>' +
                '<td class="fw-semibold wo-line-total">' + lineTotal + '</td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger wo-remove-row" data-idx="' + i + '" title="حذف">' +
                    '<i class="bi bi-x-lg"></i></button></td>' +
                '<input type="hidden" name="details[' + i + '][product_id]" value="' + d.pid + '">' +
                '<input type="hidden" name="details[' + i + '][quantity]" value="' + d.qty + '" class="wo-hid-qty" data-idx="' + i + '">' +
                '<input type="hidden" name="details[' + i + '][unit_price]" value="' + d.price.toFixed(2) + '" class="wo-hid-price" data-idx="' + i + '">' +
                '<div class="wo-hid-attrs" data-idx="' + i + '"></div>' +
                '</tr>';
            $detBody.append(html);
            _woRenderRowAttributes(i);
        }

        $grandTot.text(grand.toFixed(2));
    }

    // ══════════════════════════════════════
    //  LIVE QTY / PRICE EDIT
    // ══════════════════════════════════════
    $detBody.on('input', '.wo-detail-qty', function () {
        var idx = parseInt($(this).data('idx'));
        var val = parseFloat($(this).val()) || 0;
        _woDetails[idx].qty = val;
        _woRecalcRow(idx);
    });

    $detBody.on('input', '.wo-detail-price', function () {
        var idx = parseInt($(this).data('idx'));
        var val = parseFloat($(this).val()) || 0;
        _woDetails[idx].price = val;
        _woRecalcRow(idx);
    });

    $detBody.on('keydown', '.wo-detail-qty', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).closest('tr').find('.wo-detail-price').focus().select();
        }
    });

    $detBody.on('keydown', '.wo-detail-price', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $search.focus();
        }
    });

    function _woRecalcRow(idx) {
        var d = _woDetails[idx];
        var lineTotal = (d.qty * d.price).toFixed(2);
        $detBody.find('tr[data-idx="' + idx + '"] .wo-line-total').text(lineTotal);
        $detBody.find('.wo-hid-qty[data-idx="' + idx + '"]').val(d.qty);
        $detBody.find('.wo-hid-price[data-idx="' + idx + '"]').val(d.price.toFixed(2));
        _woRecalcGrand();
    }

    function _woRecalcGrand() {
        var g = 0;
        for (var i = 0; i < _woDetails.length; i++) {
            g += _woDetails[i].qty * _woDetails[i].price;
        }
        $grandTot.text(g.toFixed(2));
    }

    // ══════════════════════════════════════
    //  REMOVE ROW
    // ══════════════════════════════════════
    $detBody.on('click', '.wo-remove-row', function () {
        var idx = parseInt($(this).data('idx'));
        _woDetails.splice(idx, 1);
        _woRenderDetails();
    });

    // ══════════════════════════════════════
    //  CLEAR ALL
    // ══════════════════════════════════════
    $('#woClearAll').on('click', function () {
        if (_woDetails.length && !confirm('هل تريد مسح كل الأصناف؟')) return;
        _woDetails = [];
        _woRenderDetails();
        $search.val('').focus();
    });

    // ── Attributes Logic ──
    function _woRenderRowAttributes(idx) {
        var d = _woDetails[idx];
        var $row = $detBody.find('tr[data-idx="' + idx + '"]');
        var $list = $row.find('.wo-attr-list');
        var $hiddens = $row.find('.wo-hid-attrs');
        
        $list.empty();
        $hiddens.empty();
        
        d.attributes.forEach((attr, aIdx) => {
            var displayText = attr.name + (attr.value ? ': ' + attr.value : '');
            $list.append(
                '<div class="wo-attr-badge">' + 
                    '<i class="bi bi-trash remove" data-idx="' + idx + '" data-aidx="' + aIdx + '" title="حذف"></i>' + 
                    displayText + 
                '</div>'
            );
            $hiddens.append('<input type="hidden" name="details[' + idx + '][attributes][' + aIdx + '][name]" value="' + _woEsc(attr.name) + '">');
            $hiddens.append('<input type="hidden" name="details[' + idx + '][attributes][' + aIdx + '][value]" value="' + _woEsc(attr.value) + '">');
        });
    }

    $detBody.on('click', '.wo-attr-badge .remove', function() {
        var idx = $(this).data('idx');
        var aidx = $(this).data('aidx');
        _woDetails[idx].attributes.splice(aidx, 1);
        _woRenderRowAttributes(idx);
    });

    $detBody.on('keydown', '.wo-attr-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            var val = $(this).val().trim();
            if (!val) return;
            
            var idx = $(this).data('idx');
            var parts = val.split(':');
            var name = parts[0].trim();
            var value = parts.length > 1 ? parts.slice(1).join(':').trim() : '';
            
            if (name) {
                // Check if already exists in this row
                var existing = _woDetails[idx].attributes.find(a => a.name.toLowerCase() === name.toLowerCase());
                if (existing) {
                    existing.value = value;
                } else {
                    _woDetails[idx].attributes.push({ name: name, value: value });
                }
                $(this).val('');
                _woRenderRowAttributes(idx);
            }
        }
    });

    // Provide autocomplete suggestions for attribute names
    $detBody.on('focus', '.wo-attr-input', function() {
        var $input = $(this);
        if (!$input.attr('list')) {
            var listId = 'attr_list_' + Math.random().toString(36).substr(2, 9);
            $input.attr('list', listId);
            var $datalist = $('<datalist id="' + listId + '"></datalist>');
            _allAttributes.forEach(attr => {
                $datalist.append('<option value="' + _woEsc(attr.name) + ': ">');
            });
            $input.after($datalist);
        }
    });

    // ══════════════════════════════════════
    //  KEYBOARD SHORTCUTS (Global)
    // ══════════════════════════════════════
    $(document).on('keydown', function (e) {
        // F1 → Open shortcuts modal
        if (e.key === 'F1') {
            e.preventDefault();
            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('woShortcutsModal'));
            modal.toggle();
        }

        // F2 → Focus search
        if (e.key === 'F2') {
            e.preventDefault();
            $search.focus().select();
        }

        // F12 → Submit form
        if (e.key === 'F12') {
            e.preventDefault();
            if (_woDetails.length) {
                $form.submit();
            }
        }

        // Delete → Remove last item (only when not inside input)
        if (e.key === 'Delete' && !$(e.target).is('input, textarea, select')) {
            e.preventDefault();
            if (_woDetails.length) {
                _woDetails.pop();
                _woRenderDetails();
            }
        }
    });

    // Tab/Enter between qty inputs or back to search
    $detBody.on('keydown', '.wo-detail-qty', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $search.focus().select();
        }
        
        if (e.key === 'Tab' && !e.shiftKey) {
            var idx = parseInt($(this).data('idx'));
            var $nextQty = $detBody.find('.wo-detail-qty[data-idx="' + (idx + 1) + '"]');
            if ($nextQty.length) {
                e.preventDefault();
                $nextQty.select().focus();
            }
        }
    });

    // ══════════════════════════════════════
    //  FORM VALIDATION
    // ══════════════════════════════════════
    $form.on('submit', function (e) {
        var errors = [];
        
        // 1. Validate Client
        if (!$cHidden.val()) {
            errors.push('يجب اختيار عميل أولاً.');
            $cSearch.addClass('is-invalid');
        } else {
            $cSearch.removeClass('is-invalid');
        }

        // 2. Validate Products Presence
        if (!_woDetails.length) {
            errors.push('يجب إضافة صنف واحد على الأقل لأمر العمل.');
            $search.addClass('is-invalid').focus();
        } else {
            $search.removeClass('is-invalid');
        }

        // 3. Validate Quantities and Prices
        $('.wo-detail-qty').each(function() {
            if (parseFloat($(this).val()) <= 0 || !$(this).val()) {
                $(this).addClass('is-invalid');
                if (errors.indexOf('يرجى التأكد من أن جميع الكميات أكبر من صفر.') === -1) {
                    errors.push('يرجى التأكد من أن جميع الكميات أكبر من صفر.');
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (errors.length > 0) {
            e.preventDefault();
            
            // Show errors in an alert (or you can use a toast if available)
            // For now, let's use a simple but clear alert
            alert(errors.join('\n'));
            
            return false;
        }

        // Disable save button to prevent double submit
        $('#woSaveBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جاري الحفظ...');
    });

    // ══════════════════════════════════════
    //  UTILITIES
    // ══════════════════════════════════════
    function _woEsc(str) {
        if (!str) return '';
        return $('<span>').text(str).html();
    }

})();
</script>
@endpush
