<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WOMS') — نظام إدارة أوامر العمل</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Bootstrap 5 RTL -->
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="{{ asset('css/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/modules.css') }}" rel="stylesheet">

    @stack('styles')
    <style>
        /* تمييز الحقل الفعال */
        .form-control:focus, .form-select:focus {
            background-color: #fffaf0 !important; /* لون خلفية خفيف جداً عند التركيز */
            border-color: var(--warning) !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.1) !important;
        }
        [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus {
            background-color: #2d2a24 !important;
            border-color: var(--warning) !important;
        }

        /* تنسيقات الرسايل (Alerts) المتميزة */
        .alert-custom {
            border: none;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .alert-custom .alert-icon {
            font-size: 1.5rem;
            margin-left: 1rem;
            line-height: 1;
        }
        .alert-custom .alert-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }
        .alert-custom .alert-message, .alert-custom .alert-list {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        .alert-custom .alert-list {
            padding-right: 1rem;
        }


        .alert-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
        }
        .alert-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: #fff;
        }
        .alert-warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: #fff;
        }
        .alert-custom .btn-close {
            filter: brightness(0) invert(1);
            padding: 1.15rem;
        }

        /* ── Print Specific Styles ─────────────────────────────────── */
        @media print {
            .print-header {
                display: block !important;
            }
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                display: block !important;
            }
            body {
                background: white !important;
                padding-bottom: 50px;
            }
            .main-content {
                margin: 0 !important;
            }
            .page-content {
                padding: 0 !important;
            }
            /* ترقيم الصفحات التلقائي */
            .page-number:after {
                content: counter(page);
            }
        }
    </style>
</head>
<body>

    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-gear-wide-connected"></i>
            <span>WOMS</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">الرئيسية</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                لوحة التحكم
            </a>

            <div class="nav-label">العمليات</div>

            @if(auth()->user()->hasPermission('work-orders.view'))
            <a href="{{ route('work-orders.index') }}" class="nav-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check"></i>
                أوامر العمل
            </a>
            @endif

            @if(auth()->user()->hasPermission('order-batches.view'))
            <a href="{{ route('order-batches.index') }}" class="nav-link {{ request()->routeIs('order-batches.*') ? 'active' : '' }}">
                <i class="bi bi-collection"></i>
                دفعات الأوامر
            </a>
            @endif

            <div class="nav-label">الكتالوج</div>

            @if(auth()->user()->hasPermission('products.view'))
            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                المنتجات
            </a>
            @endif

            @if(auth()->user()->hasPermission('categories.view'))
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                التصنيفات
            </a>
            @endif

            @if(auth()->user()->hasPermission('attributes.view'))
            <a href="{{ route('attributes.index') }}" class="nav-link {{ request()->routeIs('attributes.*') ? 'active' : '' }}">
                <i class="bi bi-list-stars"></i>
                الخصائص
            </a>
            @endif

            <div class="nav-label">الأشخاص</div>

            @if(auth()->user()->hasPermission('clients.view'))
            <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                العملاء
            </a>
            @endif

            @if(auth()->user()->hasPermission('users.view'))
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                المستخدمين
            </a>
            @endif

            <div class="nav-label">التحليلات</div>

            <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i>
                التقارير
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Top Bar --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="btn-sidebar-toggle" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 fw-semibold">@yield('page-title', 'لوحة التحكم')</h5>
            </div>
            <div class="topbar-right">
                <button class="theme-toggle" id="themeToggle" type="button" title="تبديل الوضع">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ Auth::user()->name ?? 'مستخدم' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-start">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-left me-2"></i>تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            @include('partials.print-layout')
            @yield('content')
        </main>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- Bootstrap 5 JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/modules.js') }}"></script>

    @stack('scripts')
    <script>
        $(document).ready(function() {
            // التركيز التلقائي على أول حقل في أي نموذج (Create/Edit)
            $('form').find('input:visible, select:visible, textarea:visible').first().focus();

            // إخفاء رسايل النجاح تلقائياً بعد 5 ثواني
            setTimeout(function() {
                $('.auto-dismiss').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        });
    </script>
</body>
</html>
