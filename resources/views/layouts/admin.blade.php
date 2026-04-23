<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DNSC Water Refilling Station</title>
    <link rel="icon" type="image/png" href="{{ asset('img/dnsc-logo.png') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <style>
        /* ===== SKELETON LOADING ===== */
        #page-skeleton {
            position: fixed; inset: 0; z-index: 9999;
            background: var(--bs-body-bg, #fff);
            display: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }
        #page-skeleton.visible { display: flex !important; opacity: 1; }
        .sk-sidebar {
            width: 250px; height: 100vh; flex-shrink: 0;
            border-right: 1px solid rgba(0,0,0,.1);
            padding: 1.25rem;
            display: flex; flex-direction: column; gap: 0.75rem;
        }
        .sk-main { flex: 1; display: flex; flex-direction: column; }
        .sk-topbar {
            height: 60px; border-bottom: 1px solid rgba(0,0,0,.1);
            display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
        }
        .sk-content { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
        .sk-block {
            border-radius: 8px;
            background: linear-gradient(90deg, #e8e8e8 25%, #f8f8f8 50%, #e8e8e8 75%);
            background-size: 400% 100%;
            animation: sk-shimmer 1.4s ease infinite;
        }
        [data-bs-theme="dark"] .sk-block {
            background: linear-gradient(90deg, #2a2a2a 25%, #383838 50%, #2a2a2a 75%);
            background-size: 400% 100%;
        }
        [data-bs-theme="dark"] #page-skeleton { background: #1a1a2e; }
        @keyframes sk-shimmer {
            0%   { background-position: 100% 50%; }
            100% { background-position: -100% 50%; }
        }
    </style>

    <script>
        // Check local storage
        const getPreferredTheme = () => {
            const storedTheme = localStorage.getItem('theme')
            if (storedTheme) {
                return storedTheme
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
        }

        const setTheme = function (theme) {
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-bs-theme', 'dark')
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme)
            }
        }

        setTheme(getPreferredTheme())

        // Initial Sidebar State (to prevent flicker)
        if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>

    <!-- Styles moved to custom.css -->
    @stack('styles')
</head>
<body>
    <!-- Skeleton Loading Overlay -->
    <div id="page-skeleton">
        @if(auth()->check() && in_array(auth()->user()->role, ['admin','director','manager','staff']))
        <!-- Admin skeleton: sidebar + topbar + content -->
        <div class="sk-sidebar d-none d-lg-flex">
            <div class="sk-block" style="height:48px;width:80%;"></div>
            <div class="sk-block" style="height:12px;width:40%;margin-top:1rem;"></div>
            @for($i=0;$i<6;$i++)
            <div class="sk-block" style="height:38px;border-radius:10px;"></div>
            @endfor
        </div>
        <div class="sk-main">
            <div class="sk-topbar">
                <div class="sk-block" style="width:28px;height:28px;border-radius:6px;"></div>
                <div class="sk-block" style="width:160px;height:16px;"></div>
                <div class="sk-block ms-auto" style="width:38px;height:38px;border-radius:50%;"></div>
            </div>
            <div class="sk-content">
                <div class="sk-block" style="height:28px;width:40%;"></div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;">
                    @for($i=0;$i<4;$i++)
                    <div class="sk-block" style="height:100px;border-radius:12px;"></div>
                    @endfor
                </div>
                @for($i=0;$i<5;$i++)
                <div class="sk-block" style="height:44px;"></div>
                @endfor
            </div>
        </div>
        @else
        <!-- User navbar skeleton -->
        <div style="width:100%;">
            <div class="sk-topbar" style="height:64px;border-bottom:1px solid rgba(0,0,0,.1);">
                <div class="sk-block" style="width:44px;height:44px;border-radius:50%;"></div>
                <div class="sk-block" style="width:200px;height:20px;"></div>
                <div class="sk-block ms-auto" style="width:100px;height:36px;border-radius:20px;"></div>
            </div>
            <div class="sk-content">
                <div class="sk-block" style="height:32px;width:30%;"></div>
                @for($i=0;$i<6;$i++)
                <div class="sk-block" style="height:48px;"></div>
                @endfor
            </div>
        </div>
        @endif
    </div>
    <div id="app">
        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'director', 'manager', 'staff']))
            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <!-- Sidebar for Admin/Manager -->
            <div class="admin-sidebar" id="adminSidebar">
                <script>
                    if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
                        document.getElementById('adminSidebar').classList.add('collapsed');
                    }
                </script>
                <a href="{{ route('home') }}" class="admin-sidebar-header">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center me-2">
                            <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="40" class="me-1">
                            <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="40">
                        </div>
                        <div>
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;"><span class="text-dnsc-green">DNSC</span> Water Refilling Station</div>
                        </div>
                    </div>
                </a>

                <nav class="admin-sidebar-nav">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-3"></i>
                        <span>Overview</span>
                    </a>
                    <a href="{{ route('admin.history') }}" class="nav-link {{ request()->routeIs('admin.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-3"></i>
                        <span>Order History</span>
                    </a>
                    @if(in_array(auth()->user()->role, ['admin', 'director', 'manager']))
                        <a href="{{ route('inventory.index') }}" class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                            <i class="bi bi-box-seam me-3"></i>
                            <span>Inventory</span>
                        </a>
                        <a href="{{ route('ppmps.index') }}" class="nav-link {{ request()->routeIs('ppmps.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text me-3"></i>
                            <span>Procurement (PPMP)</span>
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'director']))
                    <a href="{{ route('admin.org-hub.index') }}" class="nav-link {{ request()->routeIs('admin.org-hub.index') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill me-3"></i>
                        <span>Organization Hub</span>
                    </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin']))
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-3"></i>
                            <span>Users</span>
                        </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'director', 'manager']))
                        <a href="{{ route('admin.reports.hub') }}" class="nav-link {{ request()->routeIs('admin.reports.hub') || request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph me-3"></i>
                            <span>Reports Hub</span>
                        </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'director']))
                        <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                            <i class="bi bi-journal-text me-3"></i>
                            <span>System Logs</span>
                        </a>
                    @endif

                    @if(in_array(auth()->user()->role, ['admin', 'director']))
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear me-3"></i>
                            <span>System Settings</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content Area with Topbar -->
            <div class="admin-content-wrapper" id="adminContent">
                <script>
                    if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
                        document.getElementById('adminContent').classList.add('collapsed');
                    }
                </script>
                <div class="admin-topbar">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link d-lg-none me-2 p-0" id="sidebarToggle">
                                <i class="bi bi-list fs-3"></i>
                            </button>
                            <button class="btn btn-link d-none d-lg-block me-3 p-0 text-muted" id="sidebarCollapseDesktop">
                                <i class="bi bi-list fs-4"></i>
                            </button>
                            <div class="d-none d-md-flex flex-column ms-2 border-start ps-3">
                                <div class="fw-bold text-dark lh-1 mb-1" id="current-date-admin" style="font-size: 0.85rem;"></div>
                                <div class="text-primary fw-bold small lh-1" id="current-time-admin" style="font-size: 0.75rem;"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="dropdown">
                                <a class="d-flex align-items-center text-decoration-none dropdown-toggle p-1 pe-3 rounded-pill hover-bg-light transition-all" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        <i class="bi bi-person-fill text-primary"></i>
                                    </div>
                                    <div class="text-end d-none d-md-block">
                                        <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                                        <div class="text-muted small lh-1" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-2">
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 transition-all" href="#" id="theme-toggle">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-sun-fill theme-icon-light me-2 text-warning d-none"></i>
                                                <i class="bi bi-moon-stars-fill theme-icon-dark me-2 text-primary d-none"></i>
                                                <span class="theme-text">Dark Mode</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person-gear me-2 text-primary"></i>Profile Settings
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider mx-2"></li>
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <main class="p-3 p-md-4">
                    @yield('content')
                </main>
            </div>
        @else
            <!-- Original navbar for standard users -->
            <nav class="navbar navbar-expand-md navbar-glass sticky-top shadow-sm">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                        <div class="d-flex align-items-center me-2">
                            <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="40" class="me-1">
                            <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="40">
                        </div>
                        <span class="fw-bold text-primary text-nowrap" style="font-size: 1.1rem;"><span class="text-dnsc-green">DNSC</span> Water Refilling Station</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item d-none d-lg-block ms-3">
                                <div class="d-flex flex-column border-start ps-3">
                                    <div class="fw-bold text-dark lh-1 mb-1" id="current-date-navbar" style="font-size: 0.85rem;"></div>
                                    <div class="text-primary fw-bold small lh-1" id="current-time-navbar" style="font-size: 0.75rem;"></div>
                                </div>
                            </li>
                        </ul>
                        <ul class="navbar-nav ms-auto align-items-center">
                            @if(!auth()->check() || auth()->user()->role !== 'staff')
                                <li class="nav-item me-2">
                                    <a class="btn btn-primary rounded-pill px-4" href="{{ route('orders.create') }}">Order Now</a>
                                </li>
                            @endif
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item ms-lg-2">
                                        <a class="btn btn-outline-primary rounded-pill px-4 btn-login" href="{{ route('login') }}">
                                            <i class="bi bi-person-circle me-2"></i>{{ __('Login') }}
                                        </a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown ms-lg-3">
                                    <a id="navbarDropdownAlt" class="nav-link dropdown-toggle d-flex align-items-center p-1 pe-3 rounded-pill hover-bg-light transition-all" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div class="text-start d-none d-md-block">
                                            <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                                            <div class="text-muted small lh-1" style="font-size: 0.7rem;">{{ ucfirst(Auth::user()->role) }}</div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-2">
                                        <a class="dropdown-item rounded-3 py-2 transition-all" href="#" id="theme-toggle-alt">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-sun-fill theme-icon-light me-2 text-warning d-none"></i>
                                                <i class="bi bi-moon-stars-fill theme-icon-dark me-2 text-primary d-none"></i>
                                                <span class="theme-text">Dark Mode</span>
                                            </div>
                                        </a>
                                        <a class="dropdown-item rounded-3 py-2" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-person-gear me-2 text-primary"></i>Profile Settings
                                        </a>
                                        <div class="dropdown-divider mx-2"></div>
                                        <a class="dropdown-item rounded-3 py-2 text-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form-alt').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                        </a>
                                        <form id="logout-form-alt" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                @yield('content')
            </main>

            <footer class="py-5 mt-5 border-top bg-white">
                <div class="container text-center">
                    <div class="mb-3">
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC" height="60" class="me-2">
                            <img src="{{ asset('img/basd-logo.png') }}" alt="BASD" height="60">
                        </div>
                        <p class="mb-1 fw-bold text-primary"><span class="text-dnsc-green">DNSC</span> Water Refilling Station</p>
                        <p class="small text-muted">Under the Business and Auxiliary Services Division (BASD)</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-4 text-muted small">
                        <i class="bi bi-geo-alt-fill text-secondary"></i>
                        <span>New Visayas, Panabo City</span>
                    </div>
                    <p class="mb-0 text-muted tiny">&copy; {{ date('Y') }} Davao del Norte State College. All rights reserved.</p>
                </div>
            </footer>
        @endif
    </div>
    
    <script>
        const updateThemeUI = (theme) => {
            const sunIcons = document.querySelectorAll('.theme-icon-light');
            const moonIcons = document.querySelectorAll('.theme-icon-dark');
            const themeTexts = document.querySelectorAll('.theme-text');

            if (theme === 'dark') {
                sunIcons.forEach(icon => icon.classList.remove('d-none'));
                moonIcons.forEach(icon => icon.classList.add('d-none'));
                themeTexts.forEach(text => text.innerText = 'Light Mode');
            } else {
                sunIcons.forEach(icon => icon.classList.add('d-none'));
                moonIcons.forEach(icon => icon.classList.remove('d-none'));
                themeTexts.forEach(text => text.innerText = 'Dark Mode');
            }
        }

        // Initialize UI
        updateThemeUI(getPreferredTheme());

        // Handle multiple theme toggles
        document.querySelectorAll('#theme-toggle, #theme-toggle-alt').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                localStorage.setItem('theme', newTheme);
                setTheme(newTheme);
                updateThemeUI(newTheme);
            });
        });

        // Sidebar elements
        const adminSidebar = document.getElementById('adminSidebar');
        const adminContent = document.getElementById('adminContent');
        const sidebarCollapseDesktop = document.getElementById('sidebarCollapseDesktop');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        // Sidebar toggle for desktop
        if (sidebarCollapseDesktop && adminSidebar && adminContent) {
            sidebarCollapseDesktop.addEventListener('click', () => {
                adminSidebar.classList.toggle('collapsed');
                adminContent.classList.toggle('collapsed');
                const isCollapsed = adminSidebar.classList.contains('collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
            });
        }

        // Sidebar toggle for mobile
        if (sidebarToggle && adminSidebar) {
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('show');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
            });

            // Close sidebar when clicking outside on mobile (including clicking overlay)
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && !adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    adminSidebar.classList.remove('show');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('show');
                }
            });
        }

        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateStr = now.toLocaleDateString('en-US', options);
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

            const adminDate = document.getElementById('current-date-admin');
            const adminTime = document.getElementById('current-time-admin');
            const navbarDate = document.getElementById('current-date-navbar');
            const navbarTime = document.getElementById('current-time-navbar');

            if (adminDate) adminDate.innerText = dateStr;
            if (adminTime) adminTime.innerText = timeStr;
            if (navbarDate) navbarDate.innerText = dateStr;
            if (navbarTime) navbarTime.innerText = timeStr;
        }

        setInterval(updateClock, 1000);
        updateClock();

        // Auto-dismiss success alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-success').forEach(function(alertNode) {
                    if (typeof bootstrap !== 'undefined') {
                        var alert = new bootstrap.Alert(alertNode);
                        alert.close();
                    } else {
                        alertNode.style.display = 'none';
                    }
                });
            }, 5000);
        });
    </script>
    @stack('scripts')
    <script>
        // ===== SKELETON LOADING =====
        const skeleton = document.getElementById('page-skeleton');
        function showSkeleton() {
            if (skeleton) {
                skeleton.style.display = 'flex';
                requestAnimationFrame(() => skeleton.classList.add('visible'));
            }
        }
        // Show on any internal link click
        document.addEventListener('click', function(e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank' || e.ctrlKey || e.metaKey) return;
            // Skip logout and download links
            if (a.closest('form') || a.classList.contains('dropdown-toggle')) return;
            showSkeleton();
        });
        // Show on form submit (page navigations)
        document.addEventListener('submit', function(e) {
            if (e.target.method && e.target.method.toLowerCase() !== 'get') return;
            showSkeleton();
        });
        // Hide immediately when page is ready
        window.addEventListener('pageshow', function() {
            if (skeleton) { skeleton.classList.remove('visible'); skeleton.style.display = 'none'; }
        });
    </script>
</body>
</html>
