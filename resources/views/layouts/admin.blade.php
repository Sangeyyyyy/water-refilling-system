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



    <script>
        // Initial Sidebar State (to prevent flicker)
        if (localStorage.getItem('sidebar-collapsed') === 'true' && window.innerWidth >= 992) {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    </script>

    <!-- Styles moved to custom.css -->
    @stack('styles')
</head>
<body>

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
                            @if(auth()->check() && in_array(auth()->user()->role, ['admin','director','manager','staff']))
                            {{-- Notification Bell --}}
                            <div class="dropdown" id="notifDropdownWrapper">
                                <button class="btn p-1 position-relative d-flex align-items-center justify-content-center rounded-circle" id="notifBell" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications" style="width:38px;height:38px;background:rgba(13,110,253,0.1);border:none;">
                                    <i class="bi bi-bell-fill text-primary" style="font-size:1.1rem;" id="notifBellIcon"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge" style="font-size:0.6rem;">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2 p-0" style="min-width:330px;max-width:380px;" id="notifDropdown">
                                    <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2 border-bottom">
                                        <span class="fw-bold text-dark">Notifications</span>
                                        <button class="btn btn-link btn-sm text-primary p-0 text-decoration-none" id="markAllReadBtn">Mark all read</button>
                                    </div>
                                    <ul class="list-unstyled mb-0" id="notifList" style="max-height:320px;overflow-y:auto;">
                                        <li class="text-center text-muted py-4 small" id="notifEmpty">No new notifications</li>
                                    </ul>
                                </div>
                            </div>
                            @endif
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

        // ─── Notification Bell ────────────────────────────────────────────
        (function () {
            const isAdmin = {{ auth()->check() && in_array(auth()->user()->role ?? '', ['admin','director','manager','staff']) ? 'true' : 'false' }};
            if (!isAdmin) return;

            const badge       = document.getElementById('notifBadge');
            const bellIcon    = document.getElementById('notifBellIcon');
            const notifList   = document.getElementById('notifList');
            const emptyMsg    = document.getElementById('notifEmpty');
            const markAllBtn  = document.getElementById('markAllReadBtn');
            const csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let lastCount = null; // Changed to null to avoid chime on first load of old notifications
            let audioCtx = null;

            // Simple user interaction listener to unlock audio
            const unlockAudio = () => {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                // Once unlocked/resumed, we can remove the listeners
                window.removeEventListener('click', unlockAudio);
                window.removeEventListener('touchstart', unlockAudio);
                window.removeEventListener('keydown', unlockAudio);
            };
            window.addEventListener('click', unlockAudio);
            window.addEventListener('touchstart', unlockAudio);
            window.addEventListener('keydown', unlockAudio);

            // Soft chime via Web Audio API
            function playChime() {
                try {
                    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // If still suspended, we can't play yet
                    if (audioCtx.state === 'suspended') return;

                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.4);
                    gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
                    osc.start(audioCtx.currentTime);
                    osc.stop(audioCtx.currentTime + 0.5);
                } catch(e) {
                    console.error('Chime failed:', e);
                }
            }

            function markRead(id, listItem) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                }).then(() => {
                    if (listItem) listItem.remove();
                    fetchNotifications();
                });
            }

            function renderNotifications(notifications, count) {
                // update badge
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('d-none');
                    bellIcon.classList.add('text-danger');
                    bellIcon.classList.remove('text-primary'); 
                    bellIcon.classList.remove('text-muted');
                } else {
                    badge.classList.add('d-none');
                    bellIcon.classList.remove('text-danger');
                    bellIcon.classList.remove('text-muted');
                    bellIcon.classList.add('text-primary');
                }

                // build list
                notifList.innerHTML = '';
                if (notifications.length === 0) {
                    notifList.appendChild(emptyMsg.cloneNode(true));
                    return;
                }

                notifications.forEach(n => {
                    const li = document.createElement('li');
                    li.className = 'border-bottom transition-all hover-bg-light';
                    li.innerHTML = `
                        <div class="d-flex align-items-start px-3 py-2 gap-2 notif-item" style="cursor:pointer;" onclick="window.location.href='/home?search=${n.order_id}'">
                            <div class="mt-1 flex-shrink-0">
                                <span class="badge rounded-circle bg-primary-subtle p-2"><i class="bi bi-droplet-fill text-primary"></i></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-dark fw-bold">${n.message}</div>
                                <div class="text-muted" style="font-size:0.7rem;"><i class="bi bi-clock me-1"></i>${n.created_at}</div>
                            </div>
                            <button class="btn btn-link btn-sm text-muted p-0 ms-1 flex-shrink-0 mark-read-btn" title="Mark read" data-id="${n.id}"><i class="bi bi-check2"></i></button>
                        </div>`;
                    li.querySelector('.mark-read-btn').addEventListener('click', e => {
                        e.stopPropagation();
                        markRead(n.id, li);
                    });
                    notifList.appendChild(li);
                });
            }

            function fetchNotifications() {
                fetch('/notifications', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        const newCount = data.count;
                        // Only chime if count actually increased while user is on the page
                        if (lastCount !== null && newCount > lastCount) {
                            playChime();
                        }
                        lastCount = newCount;
                        renderNotifications(data.notifications, newCount);
                    })
                    .catch(() => {});
            }

            if (markAllBtn) {
                markAllBtn.addEventListener('click', () => {
                    fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    }).then(() => { lastCount = 0; fetchNotifications(); });
                });
            }

            // Initial fetch + poll every 3 s for near-instant notifications
            fetchNotifications();
            setInterval(fetchNotifications, 3000);
        })();

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

</body>
</html>
