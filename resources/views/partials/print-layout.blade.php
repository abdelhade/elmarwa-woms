<div class="print-header d-none d-print-block">
    <div class="row align-items-center border-bottom pb-3 mb-4">
        <div class="col-4">
            <h3 class="fw-bold text-primary mb-1">شركة المروة</h3>
            <p class="text-muted small mb-0">لصناعة الكرتون والتغليف</p>
        </div>
        <div class="col-4 text-center">
            {{-- يمكنك وضع شعار الشركة هنا --}}
            <div class="print-logo">
                <i class="bi bi-box-seam" style="font-size: 3rem; color: #4f46e5;"></i>
            </div>
        </div>
        <div class="col-4 text-start">
            <p class="mb-1 small">تاريخ الطباعة: <strong>{{ date('d-m-Y') }}</strong></p>
            <p class="mb-0 small">وقت الطباعة: <strong>{{ date('h:i A') }}</strong></p>
        </div>
    </div>
    <div class="text-center mb-4">
        <h4 class="fw-bold">@yield('page-title')</h4>
    </div>
</div>

<div class="print-footer d-none d-print-block">
    <div class="border-top pt-3 mt-5">
        <div class="row text-muted small">
            <div class="col-4 text-end">
                صفحة <span class="page-number"></span>
            </div>
            <div class="col-4 text-center">
                نظام إدارة شركة المروة - جميع الحقوق محفوظة
            </div>
            <div class="col-4 text-start">
                العنوان: المنطقة الصناعية - المحلة الكبرى
            </div>
        </div>
    </div>
</div>
