@extends('layouts.master')

@section('title', 'تعديل العميل')
@section('page-title', 'تعديل العميل')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-pencil me-2"></i>تعديل: {{ $client->name }}</h4>
    <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>رجوع</a>
</div>

@include('partials.alerts')

<div class="card form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('clients.update', $client) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">اسم العميل <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $client->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $client->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">الهاتف</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $client->phone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="credit_limit" class="form-label">حد الائتمان</label>
                    <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit', $client->credit_limit) ?? '0' }}">
                    @error('credit_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="sort_order" class="form-label">الترتيب (اختياري)</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $client->sort_order ?? 1) }}">
                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">العنوان</label>
                    <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $client->address) }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
