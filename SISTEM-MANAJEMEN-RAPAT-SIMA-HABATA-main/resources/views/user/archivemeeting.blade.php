@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/meetingarchive.css') }}">
@endpush
@section('content')

<div class="app-layout">
    <main class="content-area p-4">
        <div class="container-fluid">
            <div class="row g-4">
                {{-- PROFIL --}}
                <div class="col-md-4">
                    <div class="card card-profil-glass p-4 mb-5 shadow-custom text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle" style="font-size: 80px; color: #243b61;"></i>
                        </div>

                        {{-- nanti ambil dari auth --}}
                        <h5 class="fw-bold mb-4">{{ auth()->user()->nama_lengkap ?? 'Nama User' }}</h5>

                        <div class="text-start small">
                            <p class="mb-1"><strong>Email :</strong> {{ auth()->user()->email ?? '-' }}</p>
                            <p class="mb-1"><strong>Username :</strong> {{ auth()->user()->username ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- KEHADIRAN --}}
                    <div class="card card-soft-white p-4 shadow-custom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold">Kehadiran</h5>
                            <span class="small text-end">
                                Dari {{ $totalRapat ?? 0 }}<br>Rapat
                            </span>
                        </div>

                        <div class="d-flex justify-content-center py-3">
                            <div style="width:150px;height:150px;border-radius:50%;
                            background:linear-gradient(180deg,#0f2027,#203a43,#2c5364 60%,#d1d5db 0);
                            display:flex;align-items:center;justify-content:center;">

                                <div style="width:100px;height:100px;background:#fcfcfc;border-radius:50%;
                                display:flex;align-items:center;justify-content:center;text-align:center;
                                font-size:14px;font-weight:bold;">
                                    {{ $persentase ?? 0 }}%<br>Hadir
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-md-8">

                    {{-- CARD --}}
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

                    {{-- HISTORI --}}
                    <div class="card card-soft-white p-4 shadow-custom" style="min-height:450px;">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-clock-history me-2"></i> Histori Rapat
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-arsip-revisi align-middle">
                                <thead>
                                    <tr>
                                        <th>Id Rapat</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Usn Notulen</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse($rapats as $rapat)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                        <td>{{ $rapat->judul_rapat }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($rapat->tanggal)->format('d-m-y') }}
                                        </td>
                                        <td class="text-center">{{ $rapat->notulis->nama_lengkap ?? '-' }}</td>
                                    </tr>

                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Belum ada histori rapat
                                        </td>
                                    </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

@endsection
