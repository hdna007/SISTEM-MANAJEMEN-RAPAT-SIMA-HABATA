@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/arsip-notulen.css') }}">
@endpush
@section('content')
<div class="utama animate__animated animate__fadeIn" style="animation-delay: 0.1s;">
    <div class="container-custom">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card card-1">
                    <div class="stat-label">Total Rapat</div>
                    <div class="stat-val">{{ $totalRapat }}</div>
                    <div class="small">Semua rapat</div>
                    <i class='bx bxs-Layer'></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-2">
                    <div class="stat-label">Rapat Terlaksana</div>
                    <div class="stat-val">{{ $rapatTerlaksana }}</div>
                    <div class="small">Selesai dikonfirmasi</div>
                    <i class='bx bxs-check-shield'></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card card-3">
                    <div class="stat-label">Jumlah user yang mengikuti rapat</div>
                    <div class="stat-val">{{ $totalPeserta }}</div>
                    <div class="small">on going..</div>
                    <i class='bx bxs-time-five'></i>
                </div>
            </div>
        </div>


        <div class="container-fluid">
            <div class="card table-card">
                <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="fw-bold m-0" style="color: var(--primary-green);">Arsip Rapat</h4>
                        <p class="text-muted small m-0">Manajemen data hasil rapat yang telah terlaksana.</p>
                    </div>
                    <form method="GET" action="{{ route('admin.arsip') }}" class="d-flex gap-2 align-items-center mt-2 mt-md-0">

                        <select name="status" class="form-select form-select-sm border shadow-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="berlangsung" {{ request('status')=='berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                            <option value="menunggu" {{ request('status')=='menunggu' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Sudah Diapprove</option>
                        </select>

                        <input type="date" name="date" class="form-control form-control-sm border shadow-sm"
                            value="{{ request('date') }}">

                        <input type="text" name="search"
                            class="form-control form-control-sm border shadow-sm"
                            placeholder="Cari judul..."
                            value="{{ request('search') }}"
                            autocomplete="off">

                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bx bx-search"></i>
                        </button>

                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table custom-table" id="tableRapat">
                        <thead>
                            <tr>
                                <th>Judul Rapat</th>
                                <th>Tanggal Terlaksana</th>
                                <th>Detail Rapat</th>
                                <th>Preview PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notulenList as $index => $data)
                            <tr>
                                <td data-label="Judul Rapat">
                                    <div class="fw-bold judul-rapat">{{ $data->judul_rapat }}</div>
                                    <span class="text-muted" style="font-size: 11px;">
                                        ID: {{ $data->no_transaksi }}
                                    </span>
                                </td>

                                <td data-label="Tanggal">
                                    {{ \Carbon\Carbon::parse($data->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }}
                                </td>

                                <td data-label="Detail Rapat">
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalRapat{{ $data->no_transaksi }}">
                                        <i class="bx bx-info-circle"></i> Lihat Detail
                                    </button>
                                </td>

                                <td data-label="Preview PDF">
                                    @if($data->notulen && $data->notulen->pdf_file)
                                        <a href="{{ route('notulen.pdf', $data->no_transaksi) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                                            <i class='bi bi-file-earmark-pdf'></i> Preview PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Belum ada</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada rapat yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($notulenList as $data)
<div class="modal fade" id="modalRapat{{ $data->no_transaksi }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- HEADER -->
            <div class="modal-header border-0 pb-0">
                <h4 class="fw-bold text-dark">
                    <i class="bi bi-journal-text me-2 text-primary"></i>
                    Detail Rapat
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body pt-3">

                <!-- Judul -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label class="text-muted small">Judul Rapat</label>
                        <div class="fw-bold fs-5 text-dark">
                            {{ $data->judul_rapat }}
                        </div>
                    </div>
                </div>

                <!-- Informasi -->
                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <label class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Tanggal
                                </label>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <label class="text-muted small">
                                    <i class="bi bi-door-open me-1"></i>
                                    Ruangan
                                </label>
                                <div class="fw-semibold">
                                    {{ $data->room->nama_ruangan ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <label class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>
                                    Waktu Mulai
                                </label>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <label class="text-muted small">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Waktu Selesai
                                </label>
                                <div class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Pengaju -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <label class="text-muted small">
                            <i class="bi bi-person me-1"></i>
                            Nama Pengaju
                        </label>
                        <div class="fw-semibold">
                            {{ $data->user->nama_lengkap ?? '-' }}
                        </div>
                    </div>
                </div>

                <!-- Peserta Rapat -->
                <div class="card border-0 shadow-sm mt-3 mb-3">
                    <div class="card-body">
                        <label class="text-muted small fw-bold">
                            <i class="bi bi-people me-1"></i>
                            Peserta Rapat
                        </label>
                        <div class="mt-2">
                            @php
                                $participants = $data->participants ?? collect();
                                $sortedParticipants = $participants->sortBy(function($p) {
                                    $role = strtolower($p->pivot->peran_peserta ?? '');
                                    if ($role === 'pemimpin_rapat') return 1;
                                    if ($role === 'notulen') return 2;
                                    return 3;
                                });
                            @endphp
                            @forelse ($sortedParticipants as $p)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <div class="fw-semibold mb-0" style="font-size: 14px;">{{ $p->nama_lengkap }}</div>
                                        <small class="text-muted" style="font-size: 12px;">{{ $p->username }}</small>
                                    </div>
                                    <span class="badge bg-{{ 
                                        ($p->pivot->peran_peserta == 'pemimpin_rapat') ? 'danger' :
                                        (($p->pivot->peran_peserta == 'notulen') ? 'info' : 'secondary')
                                    }}" style="font-size: 11px;">
                                        {{ ucwords(str_replace('_', ' ', $p->pivot->peran_peserta)) }}
                                    </span>
                                </div>
                            @empty
                                <div class="text-center text-muted small py-2">
                                    Tidak ada peserta rapat
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endforeach
@endsection