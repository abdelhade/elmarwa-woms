@extends('layouts.master')

@section('title', 'أوامر العمل')
@section('page-title', 'أوامر العمل')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-clipboard2-check me-2"></i>أوامر العمل</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة القائمة (F4)
        </button>
        <a href="{{ route('work-orders.create') }}" class="btn btn-primary" id="btnAddNew"><i class="bi bi-plus-lg me-1"></i>إضافة أمر عمل (F3)</a>
    </div>
</div>

@include('partials.alerts')

<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('work-orders.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">رقم الأمر</label>
                    <input type="text" name="search" class="form-control" placeholder="رقم..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">العميل</label>
                    <select name="client_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">المنتج</label>
                    <select name="product_id" class="form-select">
                        <option value="">الكل</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s->value }}" {{ request('status') == $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>بحث</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-reset-filter"><i class="bi bi-x-lg me-1"></i>مسح</button>
            </div>
        </form>
    </div>
</div>

<div class="card data-table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>رقم الأمر</th>
                    <th>العميل</th>
                    <th>المنتج</th>
                    <th>الكمية</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $order)
                    <tr>
                        <td class="fw-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->client->name ?? '—' }}</td>
                        <td>{{ $order->product->name ?? '—' }}</td>
                        <td>{{ number_format((float)$order->quantity, 0) }}</td>
                        <td>{{ number_format((float)$order->total_price, 2) }}</td>
                        <td>
                            <div class="dropdown d-inline">
                                <button class=" dropdown-toggle badge-{{ $order->status->value }} rounded-pill px-2 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:0.9rem; border:none;">
                                    {{ $order->status->label() }}
                                </button>
                                <ul class="dropdown-menu shadow-sm" style="font-size:0.8rem; min-width:auto;">
                                    @foreach(\App\Enums\WorkOrderStatus::cases() as $s)
                                        <li>
                                            <form action="{{ route('work-orders.update-status', $order) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $s->value }}">
                                                <button type="submit" class="dropdown-item py-1 {{ $order->status->value == $s->value ? 'active' : '' }}">{{ $s->label() }}</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        <td style="font-size:0.8rem;">{{ $order->created_at?->format('d/m/Y') }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('work-orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="عرض"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('work-orders.edit', $order) }}" class="btn btn-sm btn-outline-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('work-orders.print-detailed', $order) }}" class="btn btn-sm btn-outline-info" title="طباعة مفصل" target="_blank"><i class="bi bi-printer"></i></a>
                            <a href="{{ route('work-orders.print-summary', $order) }}" class="btn btn-sm btn-outline-secondary" title="طباعة مجمع" target="_blank"><i class="bi bi-printer-fill"></i></a>
                            <form action="{{ route('work-orders.destroy', $order) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $order->order_number }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty-state"><i class="bi bi-clipboard2-check d-block"></i><p>لا توجد أوامر عمل بعد.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($workOrders->hasPages())
        <div class="card-footer d-flex justify-content-center">{{ $workOrders->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
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

    $('.btn-reset-filter').click(function() {
        window.location.href = "{{ route('work-orders.index') }}";
    });
});
</script>
@endpush
