<aside class="sidebar">

    {{-- LOGO --}}
    <div class="brand">
        <a href="{{ auth()->user()->dashboard_route }}" class="brand-link">
            <img src="{{ asset('assets/img/logo.png') }}" class="logo mb-2">
            <div class="brand-name">
                SIMA <span>HABATA</span>
            </div>
        </a>
    </div>

    {{-- USER --}}
    <div class="d-flex justify-content-center mb-4">
        @auth
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle profile-btn fs-5"
                    type="button"
                    data-bs-toggle="dropdown">
                    <i class="bi bi-person-fill"></i>
                    <span class="username">
                        {{ auth()->user()->nama_lengkap }}
                    </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-start shadow">

                <li class="dropdown-item-text fw-bold">
                    {{ auth()->user()->username }}
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        @endauth
    </div>

    {{-- MENU --}}
    <nav class="menu">

        @can('admin-menu')
            <a href="/admin" class="menu-item {{ $active === 'admin' ? 'active' : '' }}">
                <i class="bx bx-dashboard-alt fs-4"></i>
                <span>Dashboard</span>
            </a>

            <a href="/admin/arsip-rapat" class="menu-item {{ $active === 'arsip-rapat' ? 'active' : '' }}">
                <i class="bx bx-archive"></i>
                <span>Arsip Rapat</span>
            </a>
        @endcan

        @can('proktor-menu')
            <a href="/proktor" class="menu-item {{ $active === 'proktor' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        @endcan

        @can('user-menu')
        <a href="/dashboard" class="menu-item {{ $active === 'dashboard' ? 'active' : '' }}">
            <i class="bi bi-calendar"></i>
            <span>Lihat Jadwal</span>
        </a>

        <a href="/data-rapat" class="menu-item {{ $active === 'data-rapat' ? 'active' : '' }}">
            <i class="bi bi-database"></i>
            <span>Data Rapat</span>
        </a>
        @endcan

        {{-- @can('sekretaris-menu')
        <a href="/approver" class="menu-item {{ $active === 'approver' ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Dashboard</span>
        </a>
        @endcan --}}



        
        @if (auth()->user()->bookingParticipants()->where('peran_peserta', 'notulen')->exists())
            <a href="{{ route('notulen.index') }}" 
            class="menu-item {{ $active === 'notulen' ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Notulen</span>
            </a>
        @endif

    </nav>

    <div class="text-center mb-3">
        <button id="desktopToggle" class="btn btn-outline-light">
            <i id="desktopToggleIcon" class="bi bi-chevron-left"></i>
        </button>
    </div>
</aside>