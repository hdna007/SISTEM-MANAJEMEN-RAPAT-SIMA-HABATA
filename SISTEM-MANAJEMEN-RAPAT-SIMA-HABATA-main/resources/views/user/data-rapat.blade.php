    @extends('layouts.app')

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/data-rapat.css') }}">
    @endpush

    @section('content')
    <div class="container mt-4">
        <div class="layout-header">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card card-soft-white p-3 shadow-custom d-flex flex-row align-items-center justify-content-between">
                        <i class="bi bi-people fs-1" style="color:#243b61;"></i>
                        <div class="text-end">
                            <p class="mb-0 fw-semibold">Total Rapat</p>
                            <h2 class="mb-0 fw-bold">{{ $totalRapat ?? 0 }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-soft-white p-3 shadow-custom d-flex flex-row align-items-center justify-content-between">
                        <i class="bi bi-calendar-check fs-1" style="color:#243b61"></i>
                        <div class="text-end">
                            <p class="mb-0 fw-semibold">Jadwal Hari ini</p>
                            <h2 class="mb-0 fw-bold">{{ $rapatHariIni ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                {{-- <a href="/arsip-rapat" class="btn btn-success text-center my-3" id="addRapat">
                    <i class="bi bi-archive"></i>
                    Arsip rapat
                </a> --}}
                <a href="/buat-rapat" class="btn btn-success text-center my-3" id="addRapat">
                    <i class="bi bi-plus-circle"></i>
                    Buat rapat
                </a>
            </div>
        </div>

        <div class="card table-card animate__animated animate__fadeIn mb-4" style="animation-delay: 0.1s;">
            <div class="card-header-custom">
                <h5>
                    <i class='bx bx-calendar-check text-primary'></i>
                    Data Rapat Saya
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Judul Rapat</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Status</th>
                            <th class="text-center">Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rapatList as $row)
                    <tr>
                        <td data-label="Judul Rapat">
                            <div>   
                                <div class="fw-bold judul-rapat">{{ $row->judul_rapat }}</div>
                                <div class="text-muted" style="font-size:11px;">
                                    ID: #{{ $row->no_transaksi }}
                                </div>
                            </div>  
                        </td>

                        <td data-label="Tanggal">
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($row->tanggal_mulai)->translatedFormat('d F Y') }}
                                </span>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('H:i') }}
                                </small>
                            </div>
                        </td>

                        <td data-label="Status">
                        {!! $row->getStatusBadgeAttribute() !!}
                        </td>

                        <td data-label="Detail" class="text-center">
                            <button 
                                class="btn btn-outline-primary btn-detail"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDetailRapat"

                                data-judul="{{ $row->judul_rapat }}"
                                data-jumlah-peserta="{{ $row->participants->count() }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($row->tanggal_mulai)->translatedFormat('d F Y') }}"
                                data-waktu="{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('H:i') }}"
                                data-ruangan="{{ $row->room->nama_ruangan }}"
                                data-penanggung-jawab="{{ $row->user->nama_lengkap }}"
                                data-fasilitas="{{ $row->facilities->pluck('nama_fasilitas')->join(', ') }}"
                                data-transaksi="{{ $row->no_transaksi }}"
                                id="btn-detail"
                            >
                                <i class="bi bi-search"></i> Detail
                            </button>
                        </td>
                        <td data-label="Aksi">
                        @php
                            $now = Carbon\Carbon::now('Asia/Jakarta');
                            $mulai = Carbon\Carbon::parse($row->tanggal_mulai);
                            $selesai = Carbon\Carbon::parse($row->tanggal_selesai);
                        @endphp

                        {{-- PENDING / APPROVED ADMIN --}}
                        @if ($row->status == 'pending_admin' || $row->status == 'approved_admin')

                            <div class="action-group">
                                <a href="/edit-rapat/{{ $row->no_transaksi }}" 
                                class="btn btn-action btn-edit-custom mb-2">
                                    Edit
                                </a>

                                <form action="{{ route('user.cancel', $row->no_transaksi) }}" 
                                    method="POST" 
                                    class="form-cancel d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-action btn-cancel-custom mb-2">
                                        Batal
                                    </button>
                                </form>
                            </div>


                        {{-- APPROVED PROKTOR --}}
                        @elseif ($row->status == 'approved_proktor')

                            {{-- BELUM MULAI --}}
                            @if ($now->lt($mulai))

                                <div class="action-group">
                                    <a href="/edit-rapat/{{ $row->no_transaksi }}" 
                                    class="btn btn-action btn-edit-custom mb-2">
                                        Edit
                                    </a>

                                    <form action="{{ route('user.cancel', $row->no_transaksi) }}" 
                                        method="POST" 
                                        class="form-cancel d-inline">
                                        @csrf
                                        <button type="button" class="btn btn-action btn-cancel-custom mb-2">
                                            Batal
                                        </button>
                                    </form>
                                </div>

                            {{-- SEDANG BERLANGSUNG --}}
                            @elseif ($now->between($mulai, $selesai))

                                <a href="/edit-rapat/{{ $row->no_transaksi }}" 
                                class="btn btn-action btn-edit-custom mb-2">
                                    Edit
                                </a>

                            {{-- SUDAH SELESAI --}}
                            @elseif ($row->can_isi_notulen && !$row->notulen)

                                <a href="{{ route('notulen.create', $row->no_transaksi) }}" 
                                class="btn btn-outline-success btn-sm">
                                    Isi Notulen
                                </a>

                            @elseif ($row->notulen && $row->can_isi_notulen)

                                <a href="{{ route('notulen.edit', $row->no_transaksi) }}" 
                                class="btn btn-outline-primary btn-sm">
                                    Lihat Notulen
                                </a>

                            @else

                                <span class="btn btn-secondary">-</span>

                            @endif


                        {{-- REJECTED --}}
                        @elseif ($row->status == 'rejected_admin' || $row->status == 'rejected_proktor')

                            <button 
                                type="button"
                                class="btn btn-lihat-alasan btn-alasan-custom btn-action"
                                data-bs-toggle="modal"
                                data-bs-target="#modalAlasan"
                                data-alasan="{{ $row->keterangan_tolak }}"
                            >
                                Lihat alasan
                            </button>


                        {{-- CANCELED --}}
                        @elseif ($row->status == 'canceled')

                            <form action="{{ route('user.destroy', $row->no_transaksi) }}" 
                                method="POST" 
                                class="form-delete d-inline">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-danger btn-delete">
                                    Hapus
                                </button>
                            </form>


                        {{-- DEFAULT --}}
                        @else

                            <span class="btn btn-secondary">-</span>

                        @endif

                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada data rapat
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>

                {{-- Modal alasan penolakan rapat --}}
                <div class="modal fade" id="modalAlasan" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Alasan Penolakan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p id="isiAlasan" class="mb-0 text-danger"></p>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Tutup
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- end modal penolakan rapat --}}

                <!-- Modal Detail Rapat -->
                <div class="modal fade" id="modalDetailRapat" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                    
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detail Rapat</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                        <span class="info-label">Judul Rapat</span>
                        <div class="info-value" id="dJudul"></div>
                        </div>

                        <div class="mb-3 row">
                        <div class="col-md-6">
                            <span class="info-label">Tanggal</span>
                            <div class="info-value" id="dTanggal"></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Waktu</span>
                            <div class="info-value" id="dWaktu"></div>
                        </div>
                        </div>

                        <div class="mb-3 row">
                        <div class="col-md-6">
                            <span class="info-label">Jumlah Peserta</span>
                            <div class="info-value" id="dJumlahPeserta"></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Ruangan</span>
                            <div class="info-value" id="dRuang"></div>
                        </div>
                        </div>

                        <div class="mb-3 row">
                        <div class="col-md-6">
                            <span class="info-label">Penanggung Jawab</span>
                            <div class="info-value" id="dPenanggungJawab"></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Fasilitas</span>
                            <div class="info-value" id="dFasilitas"></div>
                        </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Tutup
                        </button>
                    </div>

                    </div>
                </div>
                </div>
                {{-- end modal detail rapat --}}
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="{{ asset('js/user/data-rapat.js') }}"></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let type = @json(session('alert_type'));
                let message = @json(session('success'));

                let title = "Berhasil!";
                let icon = "success";

                if (type === "cancel") {
                    title = "Rapat Dibatalkan ❌";
                }

                if (type === "update") {
                    title = "Rapat Diperbarui ✏️";
                }

                if (type === "create") {
                    title = "Rapat Dibuat 🎉";
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#3085d6"
                });

            });
        </script>
    @endif
    @endpush
    @endsection