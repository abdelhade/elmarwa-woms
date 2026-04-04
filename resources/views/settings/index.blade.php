@extends('layouts.master')

@section('title', 'الإعدادات العامة')
@section('page-title', 'الإعدادات العامة')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-gear me-2"></i>الإعدادات العامة للنظام</h4>
</div>

@include('partials.alerts')

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="default_print_copies" class="form-label fw-bold">العدد الافتراضي لنسخ الطباعة</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-printer"></i></span>
                        <input type="number" class="form-control" id="default_print_copies" name="default_print_copies" value="{{ old('default_print_copies', $defaultPrintCopies ?? 1) }}" min="1" max="20" required>
                    </div>
                    <div class="form-text">سيتم استخدام هذا العدد كقيمة افتراضية عند طباعة أي أمر عمل (مفصل أو مجمع).</div>
                </div>
            </div>
            
            <hr>
            
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>حفظ الإعدادات</button>
        </form>
    </div>
</div>
@endsection
