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
            display: none; opacity: 0;
            transition: opacity 0.15s ease;
            flex-direction: column;
        }
        #page-skeleton.visible { display: flex !important; opacity: 1; }
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
        [data-bs-theme="dark"] #page-skeleton { background: #111; }
        @keyframes sk-shimmer {
            0%   { background-position: 100% 50%; }
            100% { background-position: -100% 50%; }
        }
    </style>

    <script>
        // Track start time for loader
        window.pageStartTime = Date.now();
        
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
    </script>
    @stack('styles')
</head>
<body>
    @php
        $showLoader = request()->routeIs('welcome') || request()->routeIs('home') || request()->is('/');
    @endphp
    @include('components.water-loader', ['show' => $showLoader])

    <!-- Skeleton Loading Overlay -->
    <div id="page-skeleton">
        <div style="height:64px;display:flex;align-items:center;padding:0 1.5rem;gap:1rem;border-bottom:1px solid rgba(0,0,0,.1);">
            <div class="sk-block" style="width:44px;height:44px;border-radius:50%;"></div>
            <div class="sk-block" style="width:180px;height:18px;"></div>
            <div class="sk-block ms-auto" style="width:110px;height:38px;border-radius:20px;"></div>
            <div class="sk-block" style="width:38px;height:38px;border-radius:50%;"></div>
        </div>
        <div style="padding:2rem;display:flex;flex-direction:column;gap:1rem;max-width:900px;margin:0 auto;width:100%;">
            <div class="sk-block" style="height:32px;width:35%;"></div>
            @for($i=0;$i<7;$i++)
            <div class="sk-block" style="height:52px;"></div>
            @endfor
        </div>
    </div>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-glass sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <div class="d-flex align-items-center me-2">
                        <img src="{{ asset('img/dnsc-logo.png') }}" alt="DNSC Logo" height="40" class="me-1">
                        <img src="{{ asset('img/basd-logo.png') }}" alt="BASD Logo" height="40">
                    </div>
                    <span class="fw-bold text-primary text-nowrap" style="font-size: 1.1rem;"><span class="text-dnsc-green">DNSC</span> Water Refilling Station</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        @php
                            $webUser = Auth::guard('web')->user();
                            $clientUser = Auth::guard('client')->user();
                            $user = $webUser ?? $clientUser;
                        @endphp

                        @if($user)

                        <li class="nav-item me-2">
                            <a class="btn btn-primary rounded-pill px-4" href="{{ route('orders.create') }}">
                                Order Now
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown ms-lg-3">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center p-1 pe-3 rounded-pill hover-bg-light transition-all" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </div>
                                <div class="d-none d-md-block text-start">
                                    <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.9rem;">{{ $user->name }}</div>
                                    <div class="text-muted small lh-1" style="font-size: 0.7rem;">{{ $webUser ? ucfirst($webUser->role) : 'Client' }}</div>
                                </div>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-2" aria-labelledby="navbarDropdown">
                                <div class="px-3 py-2 border-bottom mb-2 d-md-none">
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $webUser ? ucfirst($webUser->role) : 'Client' }}</div>
                                </div>
                                
                                @if($webUser && in_array($webUser->role, ['admin', 'director', 'manager']))
                                    <h6 class="dropdown-header text-uppercase small fw-bold ls-1 opacity-50 px-3">Operational Management</h6>
                                    <a class="dropdown-item rounded-3 py-2" href="{{ route('inventory.index') }}">
                                        <i class="bi bi-box-seam me-2 text-primary"></i>Inventory Management
                                    </a>
                                @endif

                                @if($webUser && $webUser->role === 'admin')
                                    <h6 class="dropdown-header text-uppercase small fw-bold ls-1 opacity-50 px-3">System Management</h6>
                                    <a class="dropdown-item rounded-3 py-2" href="{{ route('users.index') }}">
                                        <i class="bi bi-people me-2 text-primary"></i>User Management
                                    </a>
                                    <a class="dropdown-item rounded-3 py-2" href="{{ route('campuses.index') }}">
                                        <i class="bi bi-building me-2 text-primary"></i>Manage Campuses
                                    </a>
                                    <a class="dropdown-item rounded-3 py-2" href="{{ route('divisions.index') }}">
                                        <i class="bi bi-diagram-3 me-2 text-primary"></i>Manage Divisions
                                    </a>
                                    <a class="dropdown-item rounded-3 py-2" href="{{ route('offices.index') }}">
                                        <i class="bi bi-house me-2 text-primary"></i>Manage Units
                                    </a>
                                @endif

                                <a class="dropdown-item rounded-3 py-2" href="{{ route('home') }}">
                                    <i class="bi bi-list-task me-2 text-primary"></i>My Orders
                                </a>

                                <a class="dropdown-item rounded-3 py-2 transition-all" href="#" id="theme-toggle">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-sun-fill theme-icon-light me-2 text-warning d-none"></i>
                                        <i class="bi bi-moon-stars-fill theme-icon-dark me-2 text-primary d-none"></i>
                                        <span class="theme-text">Dark Mode</span>
                                    </div>
                                </a>
                                
                                <div class="dropdown-divider mx-2"></div>
                                
                                <a class="dropdown-item rounded-3 py-2 text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @else
                            @if (Route::has('login'))
                                <li class="nav-item ms-lg-2">
                                    <a class="btn btn-outline-primary rounded-pill px-4 btn-login" href="{{ route('login') }}">
                                        <i class="bi bi-person-circle me-2"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif
                        @endif
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
    </div>
    
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const sunIcon = themeToggle.querySelector('.theme-icon-light');
            const moonIcon = themeToggle.querySelector('.theme-icon-dark');

            const updateThemeUI = (theme) => {
                if (theme === 'dark') {
                    if (sunIcon) sunIcon.classList.remove('d-none');
                    if (moonIcon) moonIcon.classList.add('d-none');
                    if (themeToggle.querySelector('.theme-text')) {
                        themeToggle.querySelector('.theme-text').innerText = 'Light Mode';
                    }
                } else {
                    if (sunIcon) sunIcon.classList.add('d-none');
                    if (moonIcon) moonIcon.classList.remove('d-none');
                    if (themeToggle.querySelector('.theme-text')) {
                        themeToggle.querySelector('.theme-text').innerText = 'Dark Mode';
                    }
                }
            }

            // Initialize UI
            updateThemeUI(getPreferredTheme());

            themeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                localStorage.setItem('theme', newTheme);
                setTheme(newTheme);
                updateThemeUI(newTheme);
            });
        }

        // Hide loader when page is fully loaded, with a minimum delay to ensure the animation is seen
        window.addEventListener('load', function() {
            const loader = document.getElementById('water-loader-wrapper');
            const minDisplayTime = 2500; // 2.5 seconds for a satisfying "fill"
            const loadTime = Date.now() - window.pageStartTime;
            const remainingTime = Math.max(0, minDisplayTime - loadTime);

            setTimeout(() => {
                if (loader) {
                    loader.classList.add('fade-out');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }
            }, remainingTime);
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
        document.addEventListener('click', function(e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript') || a.target === '_blank' || e.ctrlKey || e.metaKey) return;
            if (a.classList.contains('dropdown-toggle')) return;
            showSkeleton();
        });
        window.addEventListener('pageshow', function() {
            if (skeleton) { skeleton.classList.remove('visible'); skeleton.style.display = 'none'; }
        });
    </script>
</body>
</html>
