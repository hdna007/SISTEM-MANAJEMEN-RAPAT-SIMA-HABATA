@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/approver/dashboard.css') }}">
@endpush
@section('content')
<main class="content-area p-4">
        <div class="user-info-text mb-4">
            <span>Welcome, Sekretaris</span>
        </div>

        <div class="row g-4 mb-5 justify-content-center">
            <div class="col-md-5">
                <div class="stats-card p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle-dark">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Permintaan Pending</h6>
                                <h1 class="mb-0 fw-bold">{{ $jumlahPending }}</h1>
                            </div>
                        </div>
                        <i class="bi bi-clipboard-check text-muted fs-1"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="stats-card p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle-dark">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Rapat hari ini</h6>
                                <h1 class="mb-0 fw-bold">{{ $rapatHariIni }}</h1>

                            </div>
                        </div>
                        <i class="bi bi-clipboard-data text-muted fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-container-table p-4 shadow-sm">
            <h4 class="fw-bold mb-4">Permintaan Rapat</h4>
            <div class="table-responsive">
                <table class="table align-middle" id="tabelRapat">
                    <thead>
                        <tr class="text-center">
                            <th>Nama Rapat</th>
                            <th>Tanggal & Waktu</th>
                            <th>Ruangan</th>
                            <th>Penanggung Jawab</th>
                            <th>aksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($pendingMeetings as $meeting)
                    <tr class="text-center">
                        <td>{{ $meeting->judul_rapat }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($meeting->tanggal_mulai)->format('d M Y, H:i') }}
                        </td>
                        <td>{{ $meeting->room->nama_ruangan ?? '-' }}</td>
                        <td>
                            <span class="badge-pj">{{ $meeting->user->nama_lengkap ?? '-' }}</span>
                        </td>
                        <td>
                            <button class="btn btn-detail"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetail"
                                data-judul="{{ $meeting->judul_rapat }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($meeting->tanggal_mulai)->format('d M Y, H:i') }}"
                                data-ruangan="{{ $meeting->room->nama_ruangan ?? '-' }}"
                                data-pj="{{ $meeting->user->nama_lengkap ?? '-' }}"
                                data-jumlah="{{ $meeting->participants->count() ?? '-' }}"
                                data-fasilitas="{{ $meeting->facilities->pluck('nama_fasilitas')->join(', ') ?? '-' }}"
                            >
                                Lihat Detail
                            </button>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <form action="{{ route('approver.approve', $meeting->id_booking) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-terima">Terima</button>
                                </form>
                                <form action="{{ route('approver.reject', $meeting->id_booking) }}" method="POST">
                                    @csrf
                                    <button
                                        type="button"
                                        class="btn btn-tolak"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTolak"
                                        data-id="{{ $meeting->id_booking }}">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>


    {{-- Modal Tolak --}}
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="formTolak">
            @csrf
            <div class="modal-content p-4">
                <h5 class="fw-bold mb-3">Tolak Rapat</h5>

                <div class="mb-3">
                    <label class="form-label">Keterangan Penolakan</label>
                    <textarea name="keterangan_tolak"
                        class="form-control"
                        rows="3"
                        required></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Tolak Rapat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


    {{-- Modal Lihat detail--}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-custom-bg border-0 p-4">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Judul Rapat</div>
                        <div class="col-8">: <span id="detailJudul"></span></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Tanggal</div>
                        <div class="col-8">: <span id="detailTanggal"></span></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Jumlah Peserta</div>
                        <div class="col-8">: <span id="detailJumlah"></span></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Ruangan</div>
                        <div class="col-8">: <span id="detailRuangan"></span></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Penanggung Jawab</div>
                        <div class="col-8">: <span id="detailPJ"></span></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-semibold text-label">Fasilitas</div>
                        <div class="col-8">: <span id="detailFasilitas"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
@endsection

{{-- script for calendar --}}
@push('scripts')
    <script src="{{ asset('js/approver/dashboard.js') }}"></script>
@endpush
