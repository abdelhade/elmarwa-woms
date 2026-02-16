@extends('layouts.master')

@section('title', 'دفعات الأوامر')
@section('page-title', 'دفعات الأوامر')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-collection me-2"></i>دفعات الأوامر</h4>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i>طباعة القائمة (F4)
        </button>
        <a href="{{ route('order-batches.create') }}" class="btn btn-primary" id="btnAddNew"><i class="bi bi-plus-lg me-1"></i>إضافة دفعة (F3)</a>
    </div>
</div>

@include('partials.alerts')

<div class="card filter-bar">
    <div class="card-body">
        <form method="GET" action="{{ route('order-batches.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control" placeholder="الرقم أو الاسم..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">النوع</label>
                    <select name="type" class="form-select">
                        <option value="">الكل</option>
                        @foreach($types as $t)
                            <option value="{{ $t->value }}" {{ request('type') == $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">التاريخ</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
                    <th>رقم الدفعة</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>التاريخ</th>
                    <th>الوردية</th>
                    <th>عدد الأوامر</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td><code>{{ $batch->batch_number }}</code></td>
                        <td class="fw-semibold">{{ $batch->name }}</td>
                        <td>{{ $batch->type->label() }}</td>
                        <td>{{ \Carbon\Carbon::parse($batch->date)->format('d/m/Y') }}</td>
                        <td>{{ $batch->shift ?? '—' }}</td>
                        <td><span class="badge bg-primary">{{ $batch->work_orders_count }}</span></td>
                        <td class="actions-cell">
                            <a href="{{ route('order-batches.show', $batch) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('order-batches.edit', $batch) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('order-batches.print', $batch) }}" class="btn btn-sm btn-outline-info" target="_blank"><i class="bi bi-printer"></i></a>
                            <form action="{{ route('order-batches.destroy', $batch) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-name="{{ $batch->name }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="bi bi-collection d-block"></i><p>لا توجد دفعات بعد.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
        <div class="card-footer d-flex justify-content-center">{{ $batches->links() }}</div>
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
        window.location.href = "{{ route('order-batches.index') }}";
    });
});
</script>
@endpush
