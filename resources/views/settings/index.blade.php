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
            
            <div class="row mb-4 g-4">
                <div class="col-md-6">
                    <label for="default_print_copies_detailed" class="form-label fw-bold">العدد الافتراضي للطباعة المفصلة</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-printer"></i></span>
                        <input type="number" class="form-control" id="default_print_copies_detailed" name="default_print_copies_detailed" value="{{ old('default_print_copies_detailed', $detailedCopies ?? 1) }}" min="1" max="20" required>
                    </div>
                    <div class="form-text">هذا العدد سيظهر افتراضياً عند فتح (الطباعة المفصلة).</div>
                </div>
                <div class="col-md-6">
                    <label for="default_print_copies_summary" class="form-label fw-bold">العدد الافتراضي للطباعة المجمعة</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-printer-fill"></i></span>
                        <input type="number" class="form-control" id="default_print_copies_summary" name="default_print_copies_summary" value="{{ old('default_print_copies_summary', $summaryCopies ?? 1) }}" min="1" max="20" required>
                    </div>
                    <div class="form-text">هذا العدد سيظهر افتراضياً عند فتح (الطباعة المجمعة).</div>
                </div>
            </div>
            
            <hr>
            
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>حفظ الإعدادات</button>
        </form>
    </div>
</div>
@endsection
