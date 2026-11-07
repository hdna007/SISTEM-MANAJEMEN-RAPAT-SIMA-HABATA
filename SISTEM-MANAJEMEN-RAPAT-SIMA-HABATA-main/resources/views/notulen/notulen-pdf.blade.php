<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notulen Rapat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12px;
        line-height: 1.5;
    }

    .title {
        text-align: start;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .info-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .info-table td {
        padding: 4px;
        vertical-align: top;
    }

    .section-title {
        font-weight: bold;
        font-size: 18px;
        margin-top: 20px;
        margin-bottom: 8px;
    }

    .list {
        margin-left: 15px;
    }

    img {
        margin-top: 10px;
    }

    
    .notulen-content h1, 
    .notulen-content h2, 
    .notulen-content h3 {
        margin: 5px 0;
        padding: 0;
        font-weight: bold;
    }
    
    .notulen-content p {
        margin: 0; 
        padding: 0;
    }

    .notulen-content ul, 
    .notulen-content ol {
        margin: 5px 0 5px 20px; 
        padding: 0;
    }

    .notulen-content li {
        margin: 0;
        padding: 0;
    }


    .info-table {
        width: 100%;
        border-collapse: collapse; 
        margin-bottom: 20px;
    }

    .info-table td {
        padding: 6px;
        border: 1px solid #000;
        vertical-align: top;
    }
    </style>
</head>

<body>

    <div class="title">
        NOTULA RAPAT {{ strtoupper($booking->judul_rapat) }}
    </div>

    <table class="info-table" border="1" cellspacing="0" cellpadding="0"> 
    <tr>
        <td width="25%">Hari / Tanggal</td>
        <td>
        {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->translatedFormat('l / d F Y') }}
        </td>
    </tr>

    <tr>
    <td>Waktu</td>
    <td>
    {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('H:i') }}
    -
    {{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('H:i') }}
    </td>
    </tr>

    <tr>
    <td>Tempat</td>
    <td>{{ $booking->room->nama_ruangan }}</td>
    </tr>

    <tr>
    <td>Pemimpin Rapat</td>
    <td>{{ $booking->pemimpin->first()->nama_lengkap }}</td>
    </tr>

    <tr>
    <td>Peserta</td>
    <td>
    @foreach($booking->participants as $i => $p)
    {{ $i+1 }}. {{ $p->nama_lengkap }} <br>
    @endforeach
    </td>
    </tr>

    <tr>
        <td>Notulis</td>
        <td>{{ $booking->notulis->nama_lengkap }}</td>
    </tr>
    <tr>
        <td>Materi</td>
        <td>{{ $notulen->hasil_materi }}</td>
    </tr>
    <tr>
        <td colspan="2" class="notulen-content">
            {!! $notulen->deskripsi_rapat_html !!}
        </td>
    </tr>
    <tr style="background-color:#d9e2f3;">
        <td colspan="2">
            <b>Poin-Poin Hasil Rapat</b>
        </td>
    </tr>
    </table>


<div style="margin-top:25px;"></div>

<div class="section-title">Dokumentasi Kegiatan</div>
<div class="section-title">Rapat {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->translatedFormat('d F Y') }}</div>

@if(count($notulen->dokumentasi_array) > 0)

<table width="100%" cellspacing="0" cellpadding="5" border="0">
<tr>

@foreach($notulen->dokumentasi_array as $i => $doc)

    @php
        $ext = pathinfo($doc, PATHINFO_EXTENSION);
        $filePath = storage_path('app/public/' . ltrim(str_replace('\\', '/', $doc), '/'));
    @endphp

    @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
        @if(file_exists($filePath))

        <td width="50%" align="center">

            <img src="{{ $filePath }}" 
                 style="width:250px; height:auto; margin-bottom:10px; border:1px solid #ddd; padding:4px;">

        </td>

        @if(($i+1) % 2 == 0)
            </tr><tr>
        @endif

        @endif
    @else
        <td colspan="2">
            File dokumentasi berupa PDF terlampir.
        </td>
    @endif

@endforeach

</tr>
</table>

@else
<p>Tidak ada dokumentasi.</p>
@endif

</body>
</html>