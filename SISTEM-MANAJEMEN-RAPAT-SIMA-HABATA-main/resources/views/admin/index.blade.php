@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush
@section('content')
    <div class="content animate__animated animate__fadeIn">
        <div class="container-custom">
            <h5 class="mb-4 mt-0 pt-3">Welcome back, adfadaf</h5>
            <div class="row">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card card-stat p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-calendar2-week stat-icon"></i>
                                        <h6 class="stat-title">Jumlah Rapat</h6>
                                        <h3 class="stat-number">{{ $totalRapat }}</h3>
                                        <small class="text-muted">Total Rapat</small>
                                    </div>
                                    <i class="bi bi-bar-chart-fill stat-chart"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card card-stat p-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-people stat-icon"></i>
                                        <h6 class="stat-title">Total User</h6>
                                        <h3 class="stat-number">{{ $totalUser }}</h3>
                                        <small class="text-muted">Admin: {{ $adminCount }} | User: {{ $userCount }} |
                                            Proktor: {{ $proktorCount }} | Sekretaris: {{ $approverCount }}</small>
                                    </div>
                                    <i class="bi bi-pie-chart-fill stat-chart"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="card shadow border-0 rounded-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-4">Permintaan Rapat</h5>

                                    <div class="table-responsive">
                                        <div class="col-12 mb-4">
                                            <div class="request-container-inline shadow">

                                                <div class="table-label-row">
                                                    <div style="flex: 2;">Judul Rapat</div>
                                                    <div style="flex: 1; text-align:center">Pengaju</div>
                                                    <div style="flex: 1; text-align:center">Aksi</div>
                                                </div>

                                                @forelse ($rapatList as $row)
                                                    <div class="request-item">
                                                        <div class="item-info">
                                                            <h6>{{ $row->judul_rapat }}</h6>
                                                            <p>
                                                                {{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('d M Y H:i') }}
                                                            </p>
                                                        </div>

                                                        <div class="item-meta">
                                                            {{ $row->user->nama_lengkap ?? '-' }}
                                                        </div>

                                                        <div class="item-action">
                                                            <button class="btn-undangan-custom btn-detail"
                                                                data-bs-toggle="modal" data-bs-target="#modalRapat"
                                                                data-judul="{{ $row->judul_rapat }}"
                                                                data-tanggal="{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('d M Y') }}"
                                                                data-mulai="{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('H:i') }}"
                                                                data-selesai="{{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('H:i') }}"
                                                                data-ruangan="{{ $row->room->nama_ruangan ?? '-' }}"
                                                                data-kapasitas="{{ $row->room->kapasitas ?? '-' }}"
                                                                data-id="{{ $row->no_transaksi }}"
                                                                data-user="{{ $row->user->nama_lengkap ?? '-' }}">
                                                                Lihat Detail
                                                            </button>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center text-muted">
                                                        Tidak ada data rapat
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-4">
                    @php
                        use Carbon\Carbon;

                        $date = Carbon::create($year, $month, 1);
                        $daysInMonth = $date->daysInMonth;
                        $startDay = $date->dayOfWeekIso % 7; 
                    @endphp

                    <div class="cal-header d-flex justify-content-between align-items-center">
                        <a href="?month={{ $month - 1 < 1 ? 12 : $month - 1 }}&year={{ $month - 1 < 1 ? $year - 1 : $year }}"
                            class="text-decoration-none text-white position-relative">

                            <span class="arrow-wrapper">
                                ◀
                                @if($hasPrevMeeting)
                                    <span class="month-dot"></span>
                                @endif
                            </span>
                        </a>

                        <span class="fw-bold">
                            {{ strtoupper($date->translatedFormat('F Y')) }}
                        </span>

                        <a href="?month={{ $month + 1 > 12 ? 1 : $month + 1 }}&year={{ $month + 1 > 12 ? $year + 1 : $year }}"
                            class="text-decoration-none text-white position-relative">

                            <span class="arrow-wrapper">
                                ▶
                                @if($hasNextMeeting)
                                    <span class="month-dot"></span>
                                @endif
                            </span>
                        </a>
                    </div>

                    <div class="cal-grid">
                        <div>S</div>
                        <div>M</div>
                        <div>T</div>
                        <div>W</div>
                        <div>T</div>
                        <div>F</div>
                        <div>S</div>

                        @for ($i = 0; $i < $startDay; $i++)
                            <div></div>
                        @endfor

                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $fullDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                $hasMeeting = in_array($i, $tanggalRapat);
                                $isSelected = ($selectedDate == $fullDate);
                            @endphp

                            <div class="cal-day {{ $hasMeeting ? 'has-meeting' : '' }} {{ $isSelected ? 'selected-day' : '' }}"
                                onclick="filterByDate('{{ $fullDate }}')">

                                {{ $i }}

                                @if($hasMeeting)
                                    <span class="dot"></span>
                                @endif
                            </div>
                        @endfor
                    </div>

                    <div class="chart-card-mini mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0" style="color:#1A3636;">Statistik Rapat {{ $year }}</h6>
                        </div>

                        <canvas id="meetingChart" height="200"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalRapat" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom animate__animated animate__zoomIn">
                <div class="modal-header border-0 pb-0">
                    <h4 class="fw-bold text-dark m-0">Detail Undangan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-4">

                    <label class="label-custom">Judul Rapat</label>
                    <div class="input-readonly-custom" id="modalJudul"></div>

                    <div class="row">
                        <div class="col-6">
                            <label class="label-custom">Tanggal</label>
                            <div class="input-readonly-custom" id="modalTanggal"></div>
                        </div>
                        <div class="col-6">
                            <label class="label-custom">Ruangan</label>
                            <div class="input-readonly-custom" id="modalRuangan"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="label-custom">Waktu Mulai</label>
                            <div class="input-readonly-custom" id="modalMulai"></div>
                        </div>
                        <div class="col-6">
                            <label class="label-custom">Waktu Selesai</label>
                            <div class="input-readonly-custom" id="modalSelesai"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label class="label-custom">Kapasitas Orang</label>
                            <div class="input-readonly-custom" id="modalKapasitas"></div>
                        </div>
                        <div class="col-6">
                            <label class="label-custom">ID Booking</label>
                            <div class="input-readonly-custom" id="modalId"></div>
                        </div>
                    </div>

                    <label class="label-custom">Nama Pengaju</label>
                    <div class="input-readonly-custom" id="modalUser"></div>

                    <div class="mt-2">
                        <form method="POST" id="formApprove">
                            @csrf
                            <button type="submit" class="btn-acc-custom">
                                Kirim ke Proktor
                            </button>
                        </form>

                        <form method="POST" id="formReject">
                            @csrf
                            <input type="hidden" name="keterangan" id="inputKeterangan" value="">
                            <button type="button" class="btn-acc-custom" id="btnReject" style="background-color: #e74c3c;">
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    draggable: false,
                    confirmButtonColor: "#1A3636",
                    background: "#ffffff",
                    backdrop: `
                        rgba(0,0,0,0.4)
                    `
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: "Ditolak!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    draggable: false,
                    confirmButtonColor: "#e74c3c",
                    background: "#ffffff"
                });
            });
        </script>
    @endif


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const buttons = document.querySelectorAll('.btn-detail');

        buttons.forEach(button => {
            button.addEventListener('click', function () {

                document.getElementById('modalJudul').textContent = this.dataset.judul;
                document.getElementById('modalTanggal').textContent = this.dataset.tanggal;
                document.getElementById('modalMulai').textContent = this.dataset.mulai;
                document.getElementById('modalSelesai').textContent = this.dataset.selesai;
                document.getElementById('modalRuangan').textContent = this.dataset.ruangan;
                document.getElementById('modalKapasitas').textContent = this.dataset.kapasitas;
                document.getElementById('modalId').textContent = this.dataset.id;
                document.getElementById('modalUser').textContent = this.dataset.user;

                document.getElementById('formApprove').action =
                    "/admin/booking/approve/" + this.dataset.id;

                document.getElementById('formReject').action =
                    "/admin/booking/reject/" + this.dataset.id;
            });
        });

        const btnReject = document.getElementById('btnReject');
        if (btnReject) {
            btnReject.addEventListener('click', function () {
                const modalEl = document.getElementById('modalRapat');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
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
                    draggable: false,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan harus diisi!'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('inputKeterangan').value = result.value;
                        document.getElementById('formReject').submit();
                    }
                });
            });
        }

        const ctx = document.getElementById('meetingChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Rapat',
                    data: @json($chartValues),
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });

    function filterByDate(date) {
        window.location.href = `?month={{ $month }}&year={{ $year }}&selected_date=${date}`;
    }


</script>