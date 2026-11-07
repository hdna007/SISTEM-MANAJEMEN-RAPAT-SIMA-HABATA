@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/data-rapat.css') }}">
@endpush

@section('content')
<div class="container mt-4">
    <div class="card table-card animate__animated animate__fadeIn mb-4" style="animation-delay: 0.1s;">
        <div class="card-header-custom">
            <h5>
                <i class='bx bx-calendar-check text-primary'></i>
                Notulen Rapat
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

                    <td data-label="Status" >
                       {!! $row->status_badge !!}
                    </td>

                    <td data-label="Detail" class="text-center">
                        @if (!$row->notulen)
                            <span class="btn btn-secondary">-</span>
                        @else
                            <a href="{{ route('notulen.pdf', $row->no_transaksi) }}"
                                class="btn btn-outline-primary btn-detail btn-sm"
                                id="btn-detail" target="_blank">
                                <i class="bi bi-eye"></i>
                                Preview PDF
                            </a>
                        @endif
                    </td>

                    <td data-label="Aksi">
                        @if(!$row->notulen)
                            {{-- BELUM ADA NOTULEN --}}
                            <a href="{{ route('notulen.create', $row->no_transaksi) }}"
                            class="btn btn-outline-success btn-sm">
                                Isi Notulen
                            </a>

                        @elseif($row->notulen)
                            {{-- SUDAH ADA --}}
                            <a href="{{ route('notulen.edit', $row->no_transaksi) }}"
                            class="btn btn-outline-warning btn-sm mb-1">
                                <i class="bi bi-pencil-square"></i>
                                Edit
                            </a>

                            <a href="{{ route('notulen.download', $row->no_transaksi) }}" 
                            class="btn btn-outline-danger btn-sm mb-1">
                            <i class="bi bi-download"></i>
                                Download PDF
                            </a>

                            {{-- <a href="{{ route('notulen.show', $row->no_transaksi) }}"
                            class="btn btn-outline-primary btn-sm">
                                Lihat
                            </a> --}}

                        @else
                            <span class="text-muted">-</span>
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
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/user/data-notulen.js') }}"></script>
@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#3085d6'
    });
});
</script>
@endif
@endpush