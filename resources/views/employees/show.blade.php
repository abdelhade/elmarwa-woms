@extends('layouts.master')

@section('title', 'تفاصيل الموظف')
@section('page-title', 'تفاصيل الموظف')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-person-badge-fill me-2"></i>{{ $employee->name }}</h4>
    <div>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>تعديل</a>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
    </div>
</div>

@include('partials.alerts')

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4 details-card">
            <div class="card-header bg-transparent fw-bold">
                <i class="bi bi-info-circle me-1"></i>المعلومات الأساسية
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 150px;">رقم الهاتف:</td>
                            <td class="fw-medium">{{ $employee->phone ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">المنصب:</td>
                            <td class="fw-medium">{{ $employee->position ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">الراتب الأساسي:</td>
                            <td class="fw-medium">{{ $employee->salary ? number_format($employee->salary, 2) . ' ج.م' : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">تاريخ التعيين:</td>
                            <td class="fw-medium">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">حالة الموظف:</td>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($employee->notes)
        <div class="card mb-4 details-card">
            <div class="card-header bg-transparent fw-bold">
                <i class="bi bi-card-text me-1"></i>ملاحظات
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $employee->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card border-danger mb-4 shadow-sm">
            <div class="card-header bg-transparent text-danger fw-bold border-danger">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>منطقة الخطر
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">حذف هذا الموظف سيؤدي إلى إزالته نهائياً من النظام.</p>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف نهائياً؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-trash me-1"></i>حذف الموظف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
