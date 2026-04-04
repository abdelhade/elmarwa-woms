@extends('layouts.master')

@section('title', 'تعديل موظف')
@section('page-title', 'تعديل موظف')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i>تعديل بيانات: {{ $employee->name }}</h4>
    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
</div>

@include('partials.alerts')

<div class="card form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $employee->phone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="position" class="form-label">المسمى الوظيفي</label>
                    <input type="text" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $employee->position) }}">
                    @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="salary" class="form-label">الراتب الأساسي</label>
                    <input type="number" step="0.01" min="0" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $employee->salary) }}">
                    @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label for="hire_date" class="form-label">تاريخ التعيين</label>
                    <input type="date" name="hire_date" id="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}">
                    @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">موظف نشط</label>
                    </div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $employee->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>حفظ التعديلات</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
