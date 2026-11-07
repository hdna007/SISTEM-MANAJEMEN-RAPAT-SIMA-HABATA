@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>Detail Notulen</h1>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5>{{ $booking->judul_rapat }}</h5>
            <p><strong>Tanggal:</strong> {{ $booking->tanggal_mulai->format('d-m-Y H:i') }} s/d {{ $booking->tanggal_selesai->format('d-m-Y H:i') }}</p>
            <p><strong>Ruangan:</strong> {{ $booking->room->nama_ruangan }}</p>
            <p><strong>Pemimpin:</strong> {{ $booking->pemimpin->first()->nama_lengkap ?? '-' }}</p>
            <p><strong>Notulis:</strong> {{ $booking->notulis->nama_lengkap }}</p>

            <h6>Peserta:</h6>
            <ul>
                @foreach ($booking->participants->where('pivot.peran_peserta','peserta') as $participant)
                    <li>{{ $participant->nama_lengkap }}</li>
                @endforeach
            </ul>

            <h6>Hasil Materi</h6>
            <p>{{ $notulen->hasil_materi }}</p>

            <h6>Deskripsi Rapat</h6>
            <p>{{ $notulen->deskripsi_rapat }}</p>

            @if(count($notulen->dokumentasi_array) > 0)
                <h6>Dokumentasi</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($notulen->dokumentasi_array as $doc)
                        @php
                            $ext = pathinfo($doc, PATHINFO_EXTENSION);
                            $filePath = asset('storage/' . ltrim(str_replace('\\', '/', $doc), '/'));
                        @endphp
                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                            <a href="{{ $filePath }}" target="_blank">
                                <img src="{{ $filePath }}" width="150" class="img-thumbnail" style="object-fit: cover;">
                            </a>
                        @else
                            <a href="{{ $filePath }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                Lihat PDF
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection