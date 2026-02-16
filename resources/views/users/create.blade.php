@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>إضافة مستخدم جديد</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-right"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" 
                               value="1" {{ old('is_admin') ? 'checked' : '' }} onchange="togglePermissions()">
                        <label class="form-check-label" for="is_admin">
                            مدير النظام (لديه جميع الصلاحيات)
                        </label>
                    </div>
                </div>

                <div id="permissions-section" style="{{ old('is_admin') ? 'display:none' : '' }}">
                    <h5 class="mb-3">الصلاحيات</h5>
                    
                    @php
                    $moduleNames = [
                        'clients' => 'العملاء',
                        'products' => 'المنتجات',
                        'categories' => 'الفئات',
                        'work-orders' => 'أوامر العمل',
                        'order-batches' => 'دفعات الطلبات',
                        'attributes' => 'الخصائص',
                        'users' => 'المستخدمين',
                    ];
                    @endphp
                    
                    @foreach($permissions as $module => $modulePermissions)
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>{{ $moduleNames[$module] ?? $module }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="{{ $permission->id }}" 
                                               id="permission_{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> حفظ
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePermissions() {
    const isAdmin = document.getElementById('is_admin').checked;
    const permissionsSection = document.getElementById('permissions-section');
    permissionsSection.style.display = isAdmin ? 'none' : 'block';
}
</script>
@endsection
