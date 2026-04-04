@extends('layouts.master')

@section('title', 'الموظفين')
@section('page-title', 'إدارة الموظفين')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-people-fill me-2"></i>قائمة الموظفين</h4>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>إضافة موظف
    </a>
</div>

@include('partials.alerts')

<div class="card table-card">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('employees.index') }}" class="d-flex gap-2 w-50">
            <input type="text" name="search" class="form-control" placeholder="بحث بالاسم، المنصب، أو الهاتف..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            @if(request('search'))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-danger" title="مسح البحث"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الاسم</th>
                        <th>المنصب</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $employee->name }}</div>
                                @if($employee->hire_date)
                                    <small class="text-muted">تاريخ التعيين: {{ $employee->hire_date->format('Y-m-d') }}</small>
                                @endif
                            </td>
                            <td>{{ $employee->position ?? '-' }}</td>
                            <td>{{ $employee->phone ?? '-' }}</td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-info" title="عرض"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-primary" title="تعديل"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">لا يوجد موظفين مسجلين حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($employees->hasPages())
        <div class="card-footer bg-transparent pb-0">
            {{ $employees->links() }}
        </div>
    @endif
</div>

@endsection
