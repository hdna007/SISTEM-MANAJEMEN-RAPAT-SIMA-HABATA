    
@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/proktor/index.css') }}">
@endpush

@section('content')
<!--BAGIAN HEADER-->
    <div class="main-container m-4">
        <header class="d-flex align-items-center justify-content-between mb-3">
            <h4>Hi Proktor!</h4>
            <div class="d-flex align-items-center gap-4">
                <div class="search-box d-none d-md-block">
                    <input type="text" id="deviceSearch" class="form-control border-0 shadow-sm" placeholder="Cari perangkat...">
                </div>
                <div class="d-flex gap-3 fs-4 text-secondary">
                    <button class="btn btn-link p-0 border-0 position-relative"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#notifOffcanvas">
                        <i class="bi bi-bell fs-5"></i>
                        @if($rapatList->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                                {{ $rapatList->count() }}
                            </span>
                        @endif
                    </button>
                    <i class='bi bi-envelope'></i>
                </div>
            </div>
        </header>
        <!--SELESAI-->


        <!--BAGIAN NOTIFIKASI-->
        <div class="drawer-notif" id="notif"></div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="notifOffcanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-bold">Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">

                <p class="text-muted small fw-bold">UNDANGAN BARU</p>

                @forelse($rapatList as $row)
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-fill fs-4 me-2"></i>
                                <div>
                                    <h6 class="m-0 fw-bold">
                                        {{ $row->user->nama_lengkap ?? '-' }}
                                    </h6>
                                    <small class="text-muted">
                                        Melampirkan Undangan
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">

                                <button class="btn btn-sm btn-outline-primary btn-detail"
                                    data-judul="{{ $row->judul_rapat }}"
                                    data-kapasitas="{{ $row->room->kapasitas ?? '-' }}"
                                    data-ruangan="{{ $row->room->nama_ruangan ?? '-' }}"
                                    data-mulai="{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('H:i') }}"
                                    data-selesai="{{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('H:i') }}"
                                    data-id="{{ $row->no_transaksi }}"
                                    data-fasilitas="{{ $row->facilities->pluck('nama_fasilitas')->join(', ') ?? '-' }}">
                                    Lihat undangan 
                                </button>

                                <form method="POST" class="formRejectProktor"
                                    action="{{ route('proktor.reject', $row->no_transaksi) }}">
                                    @csrf
                                    <input type="hidden" name="keterangan" class="inputKeterangan">
                                    <button type="button" class="btn btn-sm btn-outline-danger btnRejectProktor">
                                        Tolak
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>

                @empty
                    <p class="text-muted text-center">
                        Tidak ada invitation
                    </p>
                @endforelse

            </div>
        </div>
        <!--SELESAI-->

        <!--BAGIAN MODAL UNDANGAN-->
        <div class="modal fade" id="modalViewUndangan" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 p-4">

                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold m-0">
                            <i class="bi bi-envelope-paper text-primary me-2"></i>
                            Detail Undangan
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Judul -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <small class="text-muted">Judul Rapat</small>
                            <div id="vJudul" class="fw-bold fs-5"></div>
                        </div>
                    </div>

                    <!-- Informasi -->
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <small class="text-muted">
                                        <i class="bi bi-people me-1"></i>
                                        Kapasitas
                                    </small>
                                    <div id="vKapasitas" class="fw-semibold"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <small class="text-muted">
                                        <i class="bi bi-door-open me-1"></i>
                                        Ruangan
                                    </small>
                                    <div id="vRuangan" class="fw-semibold"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        Waktu Mulai
                                    </small>
                                    <div id="vMulai" class="fw-semibold"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Waktu Selesai
                                    </small>
                                    <div id="vSelesai" class="fw-semibold"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ID Notulen -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <small class="text-muted">
                                <i class="bi bi-hash me-1"></i>
                                ID Notulen
                            </small>
                            <div id="vID" style="font-family: monospace;" class="fw-semibold"></div>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <small class="text-muted">
                                <i class="bi bi-tools me-1"></i>
                                Fasilitas Dibutuhkan
                            </small>
                            <div id="vFasilitas" class="mt-1"></div>
                        </div>
                    </div>

                    <!-- Button -->
                    <form method="POST" id="formApproveProktor"
                        data-url="{{ route('proktor.approve', ':id') }}"
                        class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>
                            Acc Invitation
                        </button>
                    </form>

                </div>
            </div>
        </div>
        <!--SELESAI-->

        <!--BAGIAN CARD WELCOME-->
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="welcome-banner mb-4 animate-up">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-1">Let's Handle the Room</h3>
                            <p class="opacity-75">SIMA HABATA BPS Prov. Sumatera Utara <i class='bx bx-cloud'></i></p>
                            <div class="d-flex gap-4 mt-4">
                                <div>
                                    <small class="d-block opacity-75">Ruangan</small>
                                    <span class="fs-3 fw-bold">{{ $totalRooms }}</span>
                                </div>
                                <div class="border-start ps-4">
                                    <small class="d-block opacity-75">Tersedia</small>
                                    <span class="fs-3 fw-bold">{{ $availableRooms->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 d-none d-md-block text-end">
                            <i class='bi bi-people-fill' style="font-size: 120px; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
                <!--SELESAI-->


                <div class="row g-4">
                    <div class="col-md-6 animate-up" style="animation-delay: 0.1s;">
                        <div class="card p-4 h-100">
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="fw-bold m-0">Penggunaan Ruangan</h5>
                                <i class='bx bx-bolt-circle text-warning fs-3'></i>
                            </div>
                            <div class="bar-container">
                                <div class="bar" style="height: 45%;"></div>
                                <div class="bar" style="height: 70%;"></div>
                                <div class="bar" style="height: 35%;"></div>
                                <div class="bar" style="height: 90%;"></div>
                                <div class="bar active" style="height: 60%;"></div>
                            </div>
                            <div class="mt-4 p-3 rounded-4 text-center border" style="cursor: pointer;">
                                <h4 class="m-0 fw-bold text-primary" id="openCalendar" style="cursor:pointer;">
                                    {{ $selectedDate->translatedFormat('F Y') }}
                                </h4>
                                <p class="small text-primary mb-0">
                                    {{ $selectedDate->translatedFormat('d M Y') }}
                                </p>    
                            </div>
                            <input type="hidden" id="selectedDateValue" value="{{ $selectedDate->format('Y-m-d') }}">
                        </div>
                    </div>

                    <!--CARD KAPASITAS-->
                    <div class="col-md-6 animate-up" style="animation-delay: 0.2s;">
                        <div class="card p-4 h-100">
                            <h5 class="fw-bold mb-4">Kapasitas Orang</h5>
                            <div class="temp-dial">
                                <span class="temp-value" id="valDisplay">0</span>
                                <small class="text-muted">orang</small>
                            </div>
                            <div class="mt-4 px-3">
                                <input type="range" class="form-range" id="tempSlider" min="0" max="150" value="0" step="5">
                            </div>
                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnShowRoom" data-url="{{ route('proktor.filterRoom') }}">Lihat Ruangan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--BAGIAN DEVICE-->
            <div class="col-xl-4 animate-up" style="animation-delay: 0.3s;">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-4">Fasilitas</h5>

                    <div id="deviceList">
                        @foreach($facilities as $f)
                        <div class="device-card {{ $f->is_active ? 'active' : '' }}" data-name="{{ strtolower($f->nama_fasilitas) }}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-3 rounded-4 bg-white shadow-sm">
                                    @if(str_contains(strtolower($f->nama_fasilitas), 'wi-fi') || str_contains(strtolower($f->nama_fasilitas), 'wifi'))
                                        <i class='bi bi-wifi fs-3'></i>
                                    @elseif(str_contains(strtolower($f->nama_fasilitas), 'whiteboard') || str_contains(strtolower($f->nama_fasilitas), 'whiteboard'))
                                        <i class='bi bi-clipboard fs-3'></i>
                                    @elseif(str_contains(strtolower($f->nama_fasilitas), 'proyektor'))
                                        <i class='bx bx-broadcast fs-3'></i>
                                    @else
                                        <i class='bx bx-plug fs-3'></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="m-0 fw-bold">{{ $f->nama_fasilitas }}</p>
                                    <small class="text-muted">All Room</small>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <label class="switch">
                                    <input type="checkbox" class="device-toggle" data-id="{{ $f->id_facility }}" {{ $f->is_active ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <button id="btnShowMore" class="btn btn-outline-primary d-none">
                            Lainnya
                        </button>
                    </div>
                    <button class="btn-add-device mt-2" id="btnShowAdd">
                        <i class='bx bx-plus-circle me-1'></i> Tambah fasilitas baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--BAGIAN MODAL ADD DEVICE-->
    <div class="modal-overlay" id="modalAdd">
        <div class="custom-modal">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold m-0">Tambah Fasilitas</h5>
                <i class='bx bx-x fs-3' style="cursor:pointer" id="btnCloseModal"></i>
            </div>

            <label class="small fw-bold text-muted">NAMA FASILITAS</label>
            <input type="text" id="devName" class="form-control form-control-custom" placeholder="Contoh: Proyektor">
            <input type="text" id="devDescription" class="form-control form-control-custom" placeholder="Deskripsi fasilitas">

            <button class="btn-save mt-3" id="btnSaveDevice">Tambah Fasilitas</button>
        </div>
    </div>
    <!--SELESAI-->


    <!--BAGIAN KALENDER-->
    <div class="calendar-overlay" id="calendarOverlay">
        <div class="calendar-card">
            <div class="cal-header">
                <button class="btn btn-sm btn-light rounded-circle" id="prevMonth"><i class="bi bi-chevron-left"></i></button>
                <h6 class="fw-bold m-0" id="monthYearTitle"></h6>
                <button class="btn btn-sm btn-light rounded-circle" id="nextMonth"><i class="bi bi-chevron-right"></i></button>
            </div>

            <div class="cal-grid">
                <div class="day-name">M</div>
                <div class="day-name">S</div>
                <div class="day-name">S</div>
                <div class="day-name">R</div>
                <div class="day-name">K</div>
                <div class="day-name">J</div>
                <div class="day-name">S</div>
            </div>

            <div id="calendarDays" class="cal-grid mt-1"></div>
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-light rounded-pill flex-grow-1" onclick="hideCalendar()">Batal</button>
                <button class="btn btn-primary rounded-pill flex-grow-1" id="closeCalendar">Pilih</button>
            </div>
        </div>
    </div>
    <!--SELESAI-->
@endsection

<!-- MODAL FILTER RUANGAN -->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Ketersediaan Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row" id="modalRoomContainer">
                    <!-- Cards will be injected by JS -->
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="saveRoomChanges" class="btn btn-success rounded-pill px-4">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')

    <script src="{{ asset('js/proktor/dashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.btnRejectProktor').forEach(btn => {

            btn.addEventListener('click', function () {

                const form = this.closest('.formRejectProktor');
                const input = form.querySelector('.inputKeterangan');
                const offcanvasEl = document.getElementById('notifOffcanvas');
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                if (offcanvas) {
                    offcanvas.hide();
                }

                Swal.fire({
                    title: 'Alasan Penolakan',
                    input: 'textarea',
                    inputPlaceholder: 'Masukkan alasan rapat ditolak...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#95a5a6',
                    allowOutsideClick: false,
                    focusConfirm: false,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan harus diisi!'
                        }
                    }
                }).then((result) => {

                    if (result.isConfirmed) {
                        input.value = result.value;
                        form.submit();
                    }

                });

            });

        });

        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
            });
        @endif
    </script>
@endpush