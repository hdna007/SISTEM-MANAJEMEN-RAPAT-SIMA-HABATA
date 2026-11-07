@extends('layouts.app')

@section('content')
<div class="container my-4">
    
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Data belum lengkap!</strong> Silakan isi semua field yang wajib diisi.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <h3 class="mb-4 fw-bold">
        {{ isset($notulen) ? 'Edit Notulen Rapat' : 'Tambah Notulen Rapat' }}
    </h3>

    <div class="card shadow border-0">
        <div class="card-body p-4">

            @if(isset($notulen))
            <form action="{{ route('notulen.update', $booking->no_transaksi) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
            @else
            <form action="{{ route('notulen.store', $booking->no_transaksi) }}" method="POST" enctype="multipart/form-data">
                @csrf
            @endif

            <input type="hidden" name="id_booking" value="{{ $booking->id_booking }}">

            {{-- ================= INFO RAPAT ================= --}}
            <h5 class="fw-semibold mb-3 text-primary">
                Informasi Rapat
            </h5>

            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <label class="form-label fw-bold">Judul Rapat</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ $booking->judul_rapat }}" readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="datetime-local" class="form-control bg-light"
                        value="{{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('Y-m-d\TH:i') }}"
                        readonly>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="datetime-local" class="form-control bg-light"
                        value="{{ \Carbon\Carbon::parse($booking->tanggal_selesai)->format('Y-m-d\TH:i') }}"
                        readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Pemimpin Rapat</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ $booking->pemimpin->first()->nama_lengkap }}"
                        readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Notulis</label>
                    <input type="text" class="form-control bg-light"
                        value="{{ $booking->notulis->nama_lengkap }}"
                        readonly>

                    <input type="hidden" name="id_notulis"
                        value="{{ $booking->id_notulis }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Peserta</label>
                    <textarea class="form-control bg-light"
                        rows="2"
                        readonly>{{ 
                        $booking->participants
                            ->where('pivot.peran_peserta','peserta')
                            ->pluck('nama_lengkap')
                            ->implode(', ')
                    }}</textarea>
                </div>

            </div>

            {{-- ================= ISI NOTULEN ================= --}}
            <h5 class="fw-semibold mb-3 text-primary">
                Isi Notulen
            </h5>

            <div class="mb-3">
                <label class="form-label fw-bold">Hasil Materi</label>
                <textarea name="hasil_materi" 
                        class="form-control @error('hasil_materi') is-invalid @enderror" 
                        rows="5" 
                        placeholder="Tuliskan hasil materi rapat..." 
                        required>{{ old('hasil_materi', $notulen->hasil_materi ?? '') }}</textarea>
                
                @error('hasil_materi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Poin-poin hasil Rapat</label>
                <textarea id="deskripsiRapat" 
                        name="deskripsi_rapat" 
                        class="form-control @error('deskripsi_rapat') is-invalid @enderror" 
                        rows="5" 
                        placeholder="Tuliskan deskripsi jalannya rapat...">{{ old('deskripsi_rapat', $notulen->deskripsi_rapat ?? '') }}</textarea>
                
                @error('deskripsi_rapat')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <h5 class="fw-semibold mb-3 text-primary fw-bold">
                Dokumentasi
            </h5>

            <div class="mb-3">
                <input type="file"
                    name="dokumentasi_kegiatan[]"
                    id="dokumentasi_upload"
                    class="form-control"
                    accept="image/*"
                    multiple
                    {{ isset($notulen) ? '' : 'required' }}>
                <small class="text-muted">Maksimal 4 file (Foto).</small>
            </div>
            
            <div id="previewArea" class="d-flex flex-wrap gap-2 mb-3"></div>

            @if(isset($notulen) && count($notulen->dokumentasi_array) > 0)
            <div class="mb-4" id="existingImagesContainer">

                <small class="text-muted d-block mb-2">
                    Dokumentasi saat ini (<span id="existingCount">{{ count($notulen->dokumentasi_array) }}</span> file)
                </small>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($notulen->dokumentasi_array as $doc)
                        @php
                            $file = str_replace('\\', '/', $doc);
                            $filePath = asset('storage/' . ltrim($file, '/'));
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($ext), ['jpg','jpeg','png','webp']))
                            <div class="existing-image-box position-relative d-inline-block">
                                <img src="{{ $filePath }}" width="200" class="img-thumbnail" style="object-fit: cover;">
                                <input type="hidden" name="existing_images[]" value="{{ $doc }}">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-existing-btn">&times;</button>
                            </div>
                        @else
                            <div class="existing-image-box position-relative d-inline-block">
                                <a href="{{ $filePath }}" target="_blank" class="btn btn-outline-primary btn-sm my-auto">
                                    Lihat File
                                </a>
                                <input type="hidden" name="existing_images[]" value="{{ $doc }}">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 remove-existing-btn">&times;</button>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
            @endif

            <div class="d-flex justify-content-end gap-2">

                <a href="{{ url()->previous() }}" class="btn btn-light">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary px-4">
                    Simpan Notulen
                </button>

            </div>

            </form>
        </div>
    </div>
</div>


@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('dokumentasi_upload');
        const previewArea = document.getElementById('previewArea');
        let selectedFiles = [];

        if(fileInput) {
            fileInput.addEventListener('change', function(e) {
                const newFiles = Array.from(e.target.files);
                const existingBoxes = document.querySelectorAll('.existing-image-box');
                const existingCount = existingBoxes ? existingBoxes.length : 0;

                if (selectedFiles.length + newFiles.length + existingCount > 4) {
                    alert('Maksimal hanya 4 file yang dapat disertakan!');
                    this.value = ""; // Reset input
                    return;
                }

                newFiles.forEach(file => {
                    // Hindari duplikat file dengan nama yang sama
                    if (!selectedFiles.some(f => f.name === file.name)) {
                        selectedFiles.push(file);
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'position-relative d-inline-block new-preview-box';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 200px; height: 150px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1">&times;</button>
                            `;
                            
                            div.querySelector('button').onclick = function() {
                                div.remove();
                                selectedFiles = selectedFiles.filter(f => f !== file);
                                updateFileInput();
                            };
                            previewArea.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    }
                });
                updateFileInput();
            });
        }

        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }


        document.querySelectorAll('.remove-existing-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.existing-image-box').remove();
                const countSpan = document.getElementById('existingCount');
                if (countSpan) {
                    countSpan.textContent = document.querySelectorAll('.existing-image-box').length;
                }
            });
        });

        var simplemde = new SimpleMDE({
            element: document.getElementById("deskripsiRapat"),
            spellChecker: false,      // matikan pemeriksa ejaan
            autosave: {
                enabled: true,
                uniqueId: "deskripsiRapat",
                delay: 1000,
            },
            placeholder: "Tuliskan deskripsi jalannya rapat...",
            status: false,            // hilangkan status bar bawah
            toolbar: ["bold", "italic", "heading", "|", "unordered-list", "ordered-list", "|", "link", "quote", "code", "preview"]
        });
    });
</script>