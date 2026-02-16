{{-- Shared alerts partial with Premium Design --}}
<div class="alerts-container">
    @if(session('success'))
        <div class="alert alert-custom alert-success alert-dismissible fade show auto-dismiss" role="alert">
            <div class="alert-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">نجاح العملية</div>
                <div class="alert-message">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-custom alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">خطأ في التنفيذ</div>
                <div class="alert-message">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-custom alert-warning alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">خطأ في البيانات</div>
                <ul class="mb-0 alert-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
