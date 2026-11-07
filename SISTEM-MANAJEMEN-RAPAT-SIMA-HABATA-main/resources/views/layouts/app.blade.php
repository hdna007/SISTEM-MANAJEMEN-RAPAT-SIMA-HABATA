<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SIMA HABATA' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <script>
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-precollapsed');
        }
    </script>
    @stack('styles')
</head>

<body>
    <header class="topbar">
        <button id="toggleSidebar" class="icon-btn">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title">
            <a href="{{ auth()->user()->dashboard_route }}" class="text-decoration-none">SIMA <span>HABATA</span></a>
        </div>
    </header>
<div class="layout-wrapper">

    <div class="app-layout">
        @include('components.sidebar')

        <main class="content flex-grow-1">
            @yield('content')
        </main>
    </div>

    @include('components.footer')

</div>    
{{-- FOOTER --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

@stack('scripts')
<script>
    const sidebar = document.querySelector('.sidebar');
    const mobileToggle = document.getElementById('toggleSidebar');
    const desktopToggle = document.getElementById('desktopToggle');
    const desktopToggleIcon = document.getElementById('desktopToggleIcon');

    const html = document.documentElement;

    // ==============================
    // LOAD STATE
    // ==============================
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';

    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }

    // ==============================
    // MOBILE
    // ==============================
    mobileToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('show');
    });

    document.querySelector('.content')?.addEventListener('click', () => {
        sidebar?.classList.remove('show');
    });

    // ==============================
    // DESKTOP TOGGLE
    // ==============================
    desktopToggle?.addEventListener('click', () => {

        sidebar.classList.toggle('collapsed');

        const collapsedNow = sidebar.classList.contains('collapsed');

        // Simpan state
        localStorage.setItem('sidebar-collapsed', collapsedNow);

        if (collapsedNow) {
            html.classList.add('sidebar-precollapsed');
            desktopToggleIcon?.classList.remove('bi-chevron-left');
            desktopToggleIcon?.classList.add('bi-chevron-right');
        } else {
            html.classList.remove('sidebar-precollapsed'); // 🔥 INI PENTING
            desktopToggleIcon?.classList.remove('bi-chevron-right');
            desktopToggleIcon?.classList.add('bi-chevron-left');
        }
    });

    // ==============================
    // RESIZE FIX
    // ==============================
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            sidebar.classList.remove('show');
        }
    });
</script>
</body>
</html>