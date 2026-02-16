@extends('layouts.master')

@section('title', 'تعديل الدفعة')
@section('page-title', 'تعديل الدفعة')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>تعديل: {{ $orderBatch->name }}</h4>
    <a href="{{ route('order-batches.show', $orderBatch) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
</div>

@include('partials.alerts')

<div class="card form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('order-batches.update', $orderBatch) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم الدفعة <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $orderBatch->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="batch_number" class="form-label">رقم الدفعة <span class="text-danger">*</span></label>
                    <input type="text" name="batch_number" id="batch_number" class="form-control @error('batch_number') is-invalid @enderror" value="{{ old('batch_number', $orderBatch->batch_number) }}" required>
                    @error('batch_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">النوع <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        @foreach($types as $t)
                            <option value="{{ $t->value }}" {{ old('type', $orderBatch->type->value) == $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">التاريخ <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $orderBatch->date instanceof \Carbon\Carbon ? $orderBatch->date->format('Y-m-d') : $orderBatch->date) }}" required>
                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="shift" class="form-label">الوردية</label>
                    <input type="text" name="shift" id="shift" class="form-control @error('shift') is-invalid @enderror" value="{{ old('shift', $orderBatch->shift) }}">
                    @error('shift') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $orderBatch->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
