@extends('layouts.master')

@section('title', 'تعديل خاصية: ' . $attribute->name)
@section('page-title', 'تعديل خاصية')

@section('content')

@include('partials.alerts')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-warning">
                    <i class="bi bi-pencil-square me-2"></i>تعديل بيانات الخاصية
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('attributes.update', $attribute) }}" method="POST" id="attributeForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">اسم الخاصية <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $attribute->name) }}" placeholder="مثال: نوع الورق، الطول، العرض" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-bold">نوع الخاصية <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach($types as $key => $value)
                                    <option value="{{ $key }}" {{ old('type', $attribute->type) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold d-block">إعدادات إضافية</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_required" id="is_required" 
                                       role="switch" {{ old('is_required', $attribute->is_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_required">هذه الخاصية إجبارية</label>
                            </div>
                        </div>
                    </div>

                    <div id="optionsWrapper" class="mb-4 {{ old('type', $attribute->type) === 'select' ? '' : 'd-none' }}">
                        <label class="form-label fw-bold">الخيارات (لقائمة الاختيار) <span class="text-danger">*</span></label>
                        <div id="optionsList">
                            @php
                                $options = old('options', $attribute->options ?? []);
                            @endphp
                            
                            @if(count($options) > 0)
                                @foreach($options as $option)
                                    <div class="input-group mb-2 option-item">
                                        <input type="text" name="options[]" class="form-control" value="{{ $option }}" placeholder="أدخل خياراً..." required>
                                        <button type="button" class="btn btn-outline-danger btn-remove-option">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2 option-item">
                                    <input type="text" name="options[]" class="form-control" placeholder="أدخل خياراً..." required {{ old('type', $attribute->type) === 'select' ? '' : 'disabled' }}>
                                    <button type="button" class="btn btn-outline-danger btn-remove-option">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddOption">
                            <i class="bi bi-plus-lg me-1"></i>إضافة خيار آخر
                        </button>
                    </div>

                    <div class="d-flex gap-2 justify-content-end border-top pt-4">
                        <a href="{{ route('attributes.index') }}" class="btn btn-light px-4">إلغاء</a>
                        <button type="submit" class="btn btn-warning px-5">تحديث الخاصية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function toggleOptions() {
        if ($('#type').val() === 'select') {
            $('#optionsWrapper').removeClass('d-none');
            $('#optionsWrapper input').prop('disabled', false);
        } else {
            $('#optionsWrapper').addClass('d-none');
            $('#optionsWrapper input').prop('disabled', true);
        }
    }

    $('#type').on('change', toggleOptions);
    // Initial check is handled by Blade classes, but let's be safe
    toggleOptions();

    $('#btnAddOption').on('click', function() {
        const newItem = `
            <div class="input-group mb-2 option-item">
                <input type="text" name="options[]" class="form-control" placeholder="أدخل خياراً..." required>
                <button type="button" class="btn btn-outline-danger btn-remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        $('#optionsList').append(newItem);
    });

    $(document).on('click', '.btn-remove-option', function() {
        if ($('.option-item').length > 1) {
            $(this).closest('.option-item').remove();
        } else {
            $(this).siblings('input').val('');
        }
    });
});
</script>
@endpush
