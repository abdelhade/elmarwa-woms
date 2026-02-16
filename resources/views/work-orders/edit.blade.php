@extends('layouts.master')

@section('title', 'تعديل أمر العمل')
@section('page-title', 'تعديل أمر العمل')

@push('styles')
<style>
    /* ── Autocomplete Dropdown ── */
    .wo-ac-wrap { position: relative; max-width: 500px; }
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
    .table-responsive { overflow: visible !important; } 
    .wo-ac-dropdown.show, .wo-client-dropdown.show { display: block; animation: woFadeIn 0.2s ease; }
    @keyframes woFadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

    .wo-ac-dropdown table { margin-bottom: 0; font-size: 0.85rem; }
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

    /* ── Details Table ── */
    .wo-details-table .qty-input { width: 80px; text-align: center; height: 31px; }
    .wo-details-table .price-input { width: 100px; text-align: center; height: 31px; }
    .wo-details-empty { color: var(--bs-secondary); text-align: center; padding: 1rem; }
    .wo-details-empty i { font-size: 1.5rem; display: block; margin-bottom: .25rem; }

    /* ── Section Card ── */
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

<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>تعديل أمر عمل: {{ $workOrder->order_number }}</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#woShortcutsModal" title="اختصارات لوحة المفاتيح">
            <i class="bi bi-keyboard me-1"></i>اختصارات
        </button>
        <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<form method="POST" action="{{ route('work-orders.update', $workOrder) }}" id="woEditForm">
    @csrf
    @method('PUT')

    {{-- ════════ Section 1: Order Info ════════ --}}
    <div class="card form-card shadow-sm border-0">
        <div class="card-body">
            <div class="wo-section-title"><i class="bi bi-info-circle"></i> بيانات أمر العمل الأساسية</div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label for="client_search" class="form-label mb-1" style="font-size: 0.85rem;">العميل <span class="text-danger">*</span></label>
                    <div class="position-relative" id="clientSearchWrap">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" id="clientSearchInput" class="form-control form-control-sm border-start-0" placeholder="ابحث عن عميل..." autocomplete="off" value="{{ $workOrder->client->name ?? '' }}">
                            <input type="hidden" name="client_id" id="client_id_hidden" value="{{ old('client_id', $workOrder->client_id) }}">
                        </div>
                        <div class="wo-client-dropdown" id="woClientDropdown">
                            <div class="list-group list-group-flush" id="woClientList"></div>
                            <div id="woClientNoResult" class="p-3 text-center text-muted" style="display:none;">لا يوجد عملاء بهذا الاسم</div>
                        </div>
                    </div>
                    @error('client_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="order_batch_id" class="form-label mb-1" style="font-size: 0.85rem;">الدفعة (اختياري)</label>
                    <select name="order_batch_id" id="order_batch_id" class="form-select form-select-sm @error('order_batch_id') is-invalid @enderror">
                        <option value="">— بدون دفعة —</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('order_batch_id', $workOrder->order_batch_id) == $batch->id ? 'selected' : '' }}>{{ $batch->name }} ({{ $batch->batch_number }})</option>
                        @endforeach
                    </select>
                    @error('order_batch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="due_date" class="form-label mb-1" style="font-size: 0.85rem;">تاريخ التسليم</label>
                    <input type="date" name="due_date" id="due_date" class="form-control form-control-sm @error('due_date') is-invalid @enderror" value="{{ old('due_date', $workOrder->due_date?->format('Y-m-d')) }}">
                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label mb-1" style="font-size: 0.85rem;">الحالة <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select form-select-sm @error('status') is-invalid @enderror">
                        @foreach(\App\Enums\WorkOrderStatus::cases() as $s)
                            <option value="{{ $s->value }}" {{ old('status', $workOrder->status->value) == $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ════════ Section 2: Order Details ════════ --}}
    <div class="card form-card shadow-sm border-0">
        <div class="card-body">
            <div class="wo-section-title mb-2"><i class="bi bi-box-seam"></i> أصناف أمر العمل</div>

            {{-- Selected Details Table --}}
            <div class="table-responsive">
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
                        {{-- Rows updated via JS --}}
                    </tbody>
                    <tbody>
                        {{-- Inline Search Row --}}
                        <tr>
                            <td class="text-center align-middle bg-light"><i class="bi bi-search text-secondary small"></i></td>
                            <td colspan="2" class="p-0 position-relative">
                                <div class="wo-ac-wrap w-100" style="max-width: none;">
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

            <div class="row mt-2">
                <div class="col-12">
                    <textarea name="notes" id="notes" rows="1" class="form-control form-control-sm @error('notes') is-invalid @enderror" placeholder="ملاحظات إضافية...">{{ old('notes', $workOrder->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary px-3" id="woSaveBtn">
                    <i class="bi bi-check-lg me-1"></i>تحديث أمر العمل (F12)
                </button>
            </div>
        </div>
    </div>
</form>

{{-- ════════ Shortcuts Modal ════════ --}}
<div class="modal fade" id="woShortcutsModal" tabindex="-1" aria-labelledby="woShortcutsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="woShortcutsLabel"><i class="bi bi-keyboard me-2"></i>اختصارات لوحة المفاتيح</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="wo-shortcut-row"><span>التركيز على البحث</span><kbd>F2</kbd></div>
                <div class="wo-shortcut-row"><span>التنقل بين نتائج البحث</span><div><kbd>↑</kbd> <kbd>↓</kbd></div></div>
                <div class="wo-shortcut-row"><span>إضافة الصنف المحدد</span><kbd>Enter</kbd></div>
                <div class="wo-shortcut-row"><span>إغلاق قائمة البحث</span><kbd>Esc</kbd></div>
                <div class="wo-shortcut-row"><span>حذف آخر صنف مضاف</span><kbd>Delete</kbd></div>
                <div class="wo-shortcut-row"><span>التنقل بين خانات الكمية</span><kbd>Tab</kbd></div>
                <div class="wo-shortcut-row"><span>حفظ أمر العمل</span><kbd>F12</kbd></div>
                <div class="wo-shortcut-row"><span>فتح هذه النافذة</span><kbd>F1</kbd></div>
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

    // ══════════════════════════════════════
    //  DATA FROM BACKEND
    // ══════════════════════════════════════
    var _woProducts = {!! json_encode($products->map(function($p) {
        return ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku ?? '', 'category' => $p->category->name ?? '', 'price' => (float)$p->unit_price];
    })->values()) !!};
    var _woClients  = {!! json_encode($clients->map(function($c) {
        return ['id' => $c->id, 'name' => $c->name];
    })) !!};

    var _allAttributes = {!! json_encode($allAttributes->map(function($a) {
        return ['id' => $a->id, 'name' => $a->name, 'type' => $a->type, 'options' => $a->options];
    })) !!};
    
    // LOAD EXISTING DETAILS
    var _woDetails = {!! json_encode($workOrder->details->map(function($d) {
        return [
            'pid' => $d->product_id,
            'name' => $d->product->name,
            'category' => $d->product?->category?->name ?? '',
            'qty' => (float)$d->quantity,
            'price' => (float)$d->unit_price,
            'attributes' => $d->attributes ?? []
        ];
    })) !!};

    var _woAcIdx     = -1;
    var _woAcVisible = [];
    var _woClientIdx = -1;
    var _woClientVisible = [];

    // ══════════════════════════════════════
    //  DOM ELEMENTS
    // ══════════════════════════════════════
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
    var $form      = $('#woEditForm');

    // ══════════════════════════════════════
    //  INITIALIZE
    // ══════════════════════════════════════
    $(document).ready(function() {
        _woRenderDetails();
    });

    // ══════════════════════════════════════
    //  AUTOCOMPLETE Logic (Common with Create)
    // ══════════════════════════════════════
    $search.on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        _woAcIdx = -1;
        if (!q) { _woHideDropdown(); return; }
        _woAcVisible = _woProducts.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
        _woRenderDropdown(q);
    });

    $search.on('focus', function () {
        if ($(this).val().trim() && _woAcVisible.length) $dropdown.addClass('show');
    });

    function _woHideDropdown() { $dropdown.removeClass('show'); _woAcIdx = -1; }

    // ══════════════════════════════════════
    //  CLIENT Search Logic
    // ══════════════════════════════════════
    $cSearch.on('input', function () {
        var q = $(this).val().toLowerCase().trim();
        _woClientIdx = -1;
        $cHidden.val('');
        if (!q) { $cDropdown.removeClass('show'); return; }
        _woClientVisible = _woClients.filter(c => c.name.toLowerCase().includes(q));
        _woRenderClientDropdown();
    });

    function _woRenderClientDropdown() {
        $cList.empty();
        if (!_woClientVisible.length) { $cNoRes.show(); $cDropdown.addClass('show'); return; }
        $cNoRes.hide();
        _woClientVisible.forEach((c, i) => { $cList.append(`<a href="#" class="list-group-item list-group-item-action py-2" data-idx="${i}">${_woEsc(c.name)}</a>`); });
        _woClientIdx = 0;
        $cList.find('.list-group-item').first().addClass('active');
        $cDropdown.addClass('show');
    }

    $cList.on('click', '.list-group-item', function (e) { e.preventDefault(); _woSelectClient(_woClientVisible[$(this).data('idx')]); });

    function _woSelectClient(client) {
        $cSearch.val(client.name);
        $cHidden.val(client.id);
        $cDropdown.removeClass('show');
        $search.focus();
    }

    // ══════════════════════════════════════
    //  ADD / RENDER DETAILS
    // ══════════════════════════════════════
    function _woAddProduct(product) {
        if (!product) return;
        for (var i = 0; i < _woDetails.length; i++) {
            if (_woDetails[i].pid === product.id) {
                $detBody.find(`tr[data-pid="${product.id}"] .qty-input`).focus().select();
                _woHideDropdown(); $search.val('');
                return;
            }
        }
        _woDetails.push({ pid: product.id, name: product.name, category: product.category, qty: 1, price: product.price, attributes: [] });
        _woRenderDetails();
        _woHideDropdown(); $search.val('');
        $detBody.find(`tr[data-pid="${product.id}"] .qty-input`).focus().select();
    }

    function _woRenderDetails() {
        $detBody.empty();
        if (!_woDetails.length) { $detEmpty.show(); $detFoot.hide(); return; }
        $detEmpty.hide(); $detFoot.show();

        var gTotal = 0;
        _woDetails.forEach((d, i) => {
            var rowTotal = d.qty * d.price;
            gTotal += rowTotal;
            var row = `<tr data-pid="${d.pid}" data-idx="${i}">
                <td class="text-center align-middle text-muted small">${i + 1}</td>
                <td class="align-middle fw-bold">${_woEsc(d.name)} <input type="hidden" name="details[${i}][product_id]" value="${d.pid}"></td>
                <td class="align-middle text-muted small">${_woEsc(d.category)}</td>
                <td>
                    <div class="wo-attr-wrap" data-idx="${i}">
                        <div class="wo-attr-list d-flex flex-wrap"></div>
                        <input type="text" class="wo-attr-input" placeholder="أضف خاصية..." data-idx="${i}">
                    </div>
                </td>
                <td class="p-1"><input type="number" step="0.01" name="details[${i}][quantity]" class="form-control form-control-sm qty-input" value="${d.qty}" data-idx="${i}"></td>
                <td class="p-1"><input type="number" step="0.01" name="details[${i}][unit_price]" class="form-control form-control-sm price-input" value="${d.price}" data-idx="${i}"></td>
                <td class="align-middle text-end fw-bold row-total">${rowTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                <td class="text-center align-middle"><button type="button" class="btn btn-link text-danger p-0 btn-remove" data-idx="${i}"><i class="bi bi-trash"></i></button></td>
                <div class="wo-hid-attrs" data-idx="${i}"></div>
            </tr>`;
            $detBody.append(row);
            _woRenderRowAttributes(i);
        });
        $grandTot.text(gTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    $detBody.on('input', '.qty-input, .price-input', function () {
        var idx = $(this).data('idx');
        var val = parseFloat($(this).val()) || 0;
        if ($(this).hasClass('qty-input')) _woDetails[idx].qty = val;
        else _woDetails[idx].price = val;
        
        var rowTotal = _woDetails[idx].qty * _woDetails[idx].price;
        $(this).closest('tr').find('.row-total').text(rowTotal.toLocaleString(undefined, {minimumFractionDigits:2}));
        
        var gTotal = 0;
        _woDetails.forEach(d => gTotal += (d.qty * d.price));
        $grandTot.text(gTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
    });

    $detBody.on('keydown', '.qty-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).closest('tr').find('.price-input').focus().select();
        }
    });

    $detBody.on('keydown', '.price-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $search.focus();
        }
    });

    $detBody.on('click', '.btn-remove', function () {
        _woDetails.splice($(this).data('idx'), 1);
        _woRenderDetails();
    });

    function _woRenderRowAttributes(idx) {
        var d = _woDetails[idx];
        var $row = $detBody.find('tr[data-idx="' + idx + '"]');
        var $list = $row.find('.wo-attr-list');
        var $hiddens = $row.find('.wo-hid-attrs');
        
        $list.empty();
        $hiddens.empty();
        
        if (!d.attributes) d.attributes = [];
        
        d.attributes.forEach((attr, aIdx) => {
            var displayText = attr.name + (attr.value ? ': ' + attr.value : '');
            $list.append(
                '<div class="wo-attr-badge">' + 
                    '<i class="bi bi-trash remove" data-idx="' + idx + '" data-aidx="' + aIdx + '" title="حذف"></i>' + 
                    displayText + 
                '</div>'
            );
            $hiddens.append(`<input type="hidden" name="details[${idx}][attributes][${aIdx}][name]" value="${_woEsc(attr.name)}">`);
            $hiddens.append(`<input type="hidden" name="details[${idx}][attributes][${aIdx}][value]" value="${_woEsc(attr.value)}">`);
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
                var existing = _woDetails[idx].attributes.find(a => a.name.toLowerCase() === name.toLowerCase());
                if (existing) existing.value = value;
                else _woDetails[idx].attributes.push({ name: name, value: value });
                $(this).val('');
                _woRenderRowAttributes(idx);
            }
        }
    });

    $detBody.on('focus', '.wo-attr-input', function() {
        var $input = $(this);
        if (!$input.attr('list')) {
            var listId = 'attr_list_' + Math.random().toString(36).substr(2, 9);
            $input.attr('list', listId);
            var $datalist = $('<datalist id="' + listId + '"></datalist>');
            _allAttributes.forEach(attr => { $datalist.append('<option value="' + _woEsc(attr.name) + ': ">'); });
            $input.after($datalist);
        }
    });

    // ══════════════════════════════════════
    //  SHORTCUTS & UTILS (Common)
    // ══════════════════════════════════════
    function _woRenderDropdown(q) {
        $acBody.empty();
        if (!_woAcVisible.length) { $acNoRes.show(); $dropdown.addClass('show'); return; }
        $acNoRes.hide();
        _woAcVisible.forEach((p, i) => {
            var added = _woDetails.some(d => d.pid === p.id);
            var cls = added ? ' class="text-muted opacity-75"' : '';
            $acBody.append(`<tr data-acid="${i}"${cls}><td><div class="fw-bold text-dark">${_woEsc(p.name)}${added?' <small class="text-success">(مضاف)</small>':''}</div></td><td><code>${_woEsc(p.sku)}</code></td><td class="text-muted">${_woEsc(p.category)}</td><td class="text-end fw-bold text-primary">${p.price.toLocaleString(undefined, {minimumFractionDigits:2})}</td></tr>`);
        });
        _woAcIdx = 0; $acBody.find('tr').first().addClass('wo-ac-active');
        $dropdown.addClass('show');
    }

    $acBody.on('click', 'tr', function () { _woAddProduct(_woAcVisible[$(this).data('acid')]); });

    $search.on('keydown', function (e) {
        if (!$dropdown.hasClass('show')) { if (e.key === 'Enter') e.preventDefault(); return; }
        var $rows = $acBody.find('tr');
        if (e.key === 'ArrowDown') { e.preventDefault(); _woAcIdx = Math.min(_woAcIdx + 1, $rows.length - 1); _woHighlightAc($rows); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); _woAcIdx = Math.max(_woAcIdx - 1, 0); _woHighlightAc($rows); }
        else if (e.key === 'Enter') { e.preventDefault(); if (_woAcIdx >= 0) _woAddProduct(_woAcVisible[_woAcIdx]); }
        else if (e.key === 'Escape') { e.preventDefault(); _woHideDropdown(); }
    });

    function _woHighlightAc($rows) { $rows.removeClass('wo-ac-active'); var $t = $rows.eq(_woAcIdx); $t.addClass('wo-ac-active'); $t[0].scrollIntoView({ block: 'nearest' }); }

    $(document).on('keydown', function (e) {
        if (e.key === 'F2') { e.preventDefault(); $search.focus(); }
        if (e.key === 'F12' || (e.ctrlKey && e.key === 's')) { e.preventDefault(); $form.submit(); }
        if (e.key === 'F1') { e.preventDefault(); new bootstrap.Modal('#woShortcutsModal').show(); }
        if (e.key === 'Delete' && _woDetails.length && document.activeElement.tagName !== 'INPUT') { _woDetails.pop(); _woRenderDetails(); }
    });

    $form.on('submit', function (e) {
        if (!_woDetails.length) { e.preventDefault(); alert('يجب إضافة صنف واحد على الأقل.'); return; }
        $('#woSaveBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>جاري التحديث...');
    });

    function _woEsc(s) { return $('<span>').text(s).html(); }
    $(document).on('mousedown', function (e) { if (!$(e.target).closest('#clientSearchWrap').length) $cDropdown.removeClass('show'); if (!$(e.target).closest('.wo-ac-wrap').length) _woHideDropdown(); });

})();
</script>
@endpush
