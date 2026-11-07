<link rel="stylesheet" href="{{ asset('css/user/createmeeting.css') }}">
@extends('layouts.app')

@section('content')
<div class="app-layout">
        <main class="page-content fade-slide">
            <div class="content-area w-100 p-4">
                <div class="form-wrapper">
                    <h2 class="text-center fw-bold mb-5 mt-3">Buat rapat</h2>
                    @if (session('failed'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('failed') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="container">
                        <form action="{{ isset($booking) 
                                ? '/update-rapat/'.$booking->no_transaksi 
                                : '/buat-rapat' }}" method="POST" id="formMeeting">
                            @csrf
                            <div class="row g-5">
                                <div class="col-md-7">
                                    <div class="mb-4">
                                        <label class="fw-bold mb-1" for="judul_rapat">Judul Rapat</label>
                                        <input type="text" class="form-control custom-input-dark @error('judul_rapat') is-invalid @enderror" id="judul_rapat" name="judul_rapat" required autocomplete="off" autofocus value="{{ old('judul_rapat', $booking->judul_rapat ?? '') }}">
                                        @error('judul_rapat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            <label class="fw-bold mb-1" for="tanggal_mulai">Tanggal / waktu mulai</label>
                                            <input type="datetime-local" class="form-control custom-input-dark @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $booking->tanggal_mulai ?? '') }}" required>
                                            @error('tanggal_mulai')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end justify-content-center">
                                            <span class="fs-2 mb-1">-</span>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="fw-bold mb-1" for="tanggal_selesai">Tanggal / waktu selesai</label>
                                            <input type="datetime-local" class="form-control custom-input-dark @error('tanggal_selesai') is-invalid @enderror" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $booking->tanggal_selesai ?? '') }}" required>
                                            @error('tanggal_selesai')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @if (session('recommendation_time'))
                                        <div class="text-end mt-2">
                                            <button type="button"
                                                    id="btnRekomendasiWaktu"
                                                    class="btn btn-outline-primary btn-sm fw-bold">
                                                🔁 Ganti jadwal
                                            </button>

                                            <div class="small text-muted mt-1">
                                                🕒
                                                {{ \Carbon\Carbon::parse(session('recommendation_time.start'))->translatedFormat('d M Y, H:i') }}
                                                –
                                                {{ \Carbon\Carbon::parse(session('recommendation_time.end'))->format('H:i') }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    @php
                                    $selectedParticipants = old('participants',
                                        isset($booking)
                                            ? $booking->participants->mapWithKeys(function($p){
                                                return [
                                                    $p->id_user => [
                                                        'id_user' => $p->id_user,
                                                        'role' => $p->pivot->peran_peserta
                                                    ]
                                                ];
                                            })->toArray()
                                            : []
                                    );
                                    
                                    uasort($selectedParticipants, function($a, $b) {
                                        $pA = ($a['role'] === 'pemimpin_rapat') ? 1 : (($a['role'] === 'notulen') ? 2 : 3);
                                        $pB = ($b['role'] === 'pemimpin_rapat') ? 1 : (($b['role'] === 'notulen') ? 2 : 3);
                                        return $pA <=> $pB;
                                    });
                                    @endphp

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-bold">Peserta rapat</label>
                                            <a class="text-primary small text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddPeserta">+ Tambah Peserta</a>
                                        </div>
                                        <div class="table-responsive">                                            
                                            <table class="table table-bordered border-dark text-center align-middle" id="tablePeserta">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama Peserta</th>
                                                        <th>Username</th>
                                                        <th>Peran</th>
                                                        <th>Lainnya</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($selectedParticipants as $id => $p)
                                                    <tr id="peserta-{{ $p['id_user'] }}">
                                                        <td>
                                                            {{ $peserta->firstWhere('id_user', $p['id_user'])->nama_lengkap }}
                                                            <input type="hidden" name="participants[{{ $p['id_user'] }}][id_user]"
                                                                value="{{ $p['id_user'] }}">
                                                            <input type="hidden" name="participants[{{ $p['id_user'] }}][role]"
                                                                value="{{ $p['role'] }}">
                                                        </td>
                                                        <td>
                                                            {{ $peserta->firstWhere('id_user', $p['id_user'])->username }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ 
                                                                $p['role'] == 'pemimpin_rapat' ? 'danger' :
                                                                ($p['role'] == 'notulen' ? 'info' : 'secondary')
                                                            }}">
                                                                {{ str_replace('_',' ',$p['role']) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="text-center mt-2 d-none" id="togglePesertaWrapper">
                                                <button type="button" class="btn btn-link fw-bold" id="togglePeserta">
                                                    Lihat lainnya
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="mb-4">
                                        <label class="fw-bold mb-2 d-block">Ruang Rapat</label>
                                        
                                        <div class="d-flex flex-column gap-2">
                                            <select id="roomSelect"
                                                    name="id_room"
                                                    class="form-select custom-input-dark w-100"
                                                    required>
                                                <option disabled {{ old('id_room') ? '' : 'selected' }}>
                                                    Pilih Ruang Rapat
                                                </option>

                                                @php $availableRoom = false; @endphp

                                                @foreach ($ruang as $r)
                                                    @if ($r->is_active == 1 && empty($r->is_used_now))
                                                        @php $availableRoom = true; @endphp
                                                        <option value="{{ $r->id_room }}"
                                                            {{ old('id_room', $booking->id_room ?? '') == $r->id_room ? 'selected' : '' }}>
                                                            {{ $r->nama_ruangan }}
                                                        </option>
                                                    @elseif ($r->is_active == 1 && !empty($r->is_used_now))
                                                        <option disabled>
                                                            {{ $r->nama_ruangan }} (Sedang Digunakan Saat Ini)
                                                        </option>
                                                    @else
                                                        <option disabled>
                                                            {{ $r->nama_ruangan }} (Tidak Tersedia)
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if (session('recommendation_room'))
                                            <div class="text-end mt-2">
                                                <button type="button"
                                                        id="btnRekomendasiRuang"
                                                        class="btn btn-outline-success btn-sm fw-bold">
                                                    🏢 Ganti ke {{ session('recommendation_room.nama') }}
                                                </button>
                                            </div>
                                            @endif
                                            @if (!$availableRoom)                                                        
                                            <button class="btn btn-navy d-flex align-items-center justify-content-center gap-2 w-100" 
                                            style="border-radius: 10px; height: 40px;"
                                            data-bs-toggle="modal" data-bs-target="#modalCreateRoom">
                                            <i class="bi bi-plus-circle"></i> Ruangan penuh? buat ruangan
                                            </button>
                                            @endif
                                        </div>
                                    </div>


                                    @php
                                    $selectedFacilities = old('fasilitas',
                                        isset($booking)
                                            ? $booking->facilities->pluck('id_facility')->toArray()
                                            : []
                                    );
                                    @endphp

                                    <div class="card bg-light p-3 border-0 shadow-sm rounded-4">
                                        <label class="fw-bold mb-3">Fasilitas yang dibutuhkan</label>
                                        <div class="row g-3">
                                            @foreach ($fasilitas as $f)
                                                <div class="col-6 d-flex align-items-center gap-2">
                                                    <input type="checkbox" 
                                                        class="form-check-input" 
                                                        id="fasilitas_{{ $f->id_facility }}" 
                                                        name="fasilitas[]" 
                                                        value="{{ $f->id_facility }}"
                                                        {{ in_array($f->id_facility, $selectedFacilities) ? 'checked' : '' }}
                                                        {{ $f->is_active == 0 ? 'disabled' : '' }}>
                                                        
                                                        <label for="fasilitas_{{ $f->id_facility }}" 
                                                            class="form-check-label">
                                                            {{ $f->nama_fasilitas }}
                                                            @if($f->is_active == 0)
                                                                <span class="text-danger small">(Tidak tersedia)</span>
                                                            @endif
                                                        </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-5">
                                <button class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow" type="submit" name="submit">Create meeting</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
</div>


    <!--MODAL TAMBAH RUANGAN-->
    <div class="modal fade" id="modalCreateRoom" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-grey-modal p-4 shadow-lg text-white">
                <h3 class="text-center fw-bold mb-4">Buat Ruangan</h3>
                <div class="mb-3">
                    <label class="fw-semibold mb-1">Nama Ruangan</label>
                    <input id="namaRuangan" type="text" class="form-control bg-dark-input text-white border-0" placeholder="input nama ruangan">
                </div>
                <div class="mb-4">
                    <label class="fw-semibold mb-1">Lantai Ruangan</label>
                    <select id="lantaiRoom"
                            class="form-select custom-input-dark w-100"
                            required>
                        <option selected disabled>Lantai</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="text-center">
                    <button class="btn btn-success px-4 rounded-pill fw-bold" style="background-color: #00e676; border: none;">create room</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal tambah peserta --}}
    
    <div class="modal fade" id="modalAddPeserta" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-grey-modal p-4 shadow-lg text-white">
                <h3 class="text-center fw-bold mb-4">Tambah Peserta</h3>
                <div class="position-relative mb-4">
                    <input type="text" id="inputCariPeserta" class="form-control rounded-pill pe-5" placeholder="Cari nama / username.." autocomplete="off" autofocus>

                    <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-dark"></i>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless text-white align-middle" id="tablePilihanPeserta">
                        <thead class="border-bottom border-secondary">
                            <tr>
                                <th>Nama Peserta</th>
                                <th>Username</th>
                                <th>Peran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="listPesertaModal" class="d-none">
                            @foreach ($peserta as $p)
                            <tr class="row-peserta"
                                data-nama="{{ strtolower($p->nama_lengkap) }}"
                                data-username="{{ strtolower($p->username) }}">

                                <td>{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->username }}</td>
                                <td>
                                    <select class="form-select form-select-sm role-select mb-1" id="roleMeeting">
                                        <option value="peserta">Peserta</option>
                                        <option value="pemimpin_rapat">Pemimpin Rapat</option>
                                        <option value="notulen">Notulen</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-primary btn-sm btn-add-peserta mt-1"
                                            data-id="{{ $p->id_user }}"
                                            data-nama="{{ $p->nama_lengkap }}"
                                            data-username="{{ $p->username }}">
                                        Add
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
window.recommendationTime = @json(session('recommendation_time'));
window.recommendationRoom = @json(session('recommendation_room'));
</script>

@push('scripts')
    <script src="{{ asset('js/user/createmeeting.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const createRoomBtn = document.querySelector('#modalCreateRoom .btn-success');
        
        createRoomBtn.addEventListener('click', function () {
            const modal = document.getElementById('modalCreateRoom');
            const namaRuangan = modal.querySelector('input').value;
            const lantai = document.getElementById('lantaiRoom').value;

            if (!namaRuangan || !lantai) {
                alert('Harap isi semua field');
                return;
            }

            fetch("{{ route('rooms.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    nama_ruangan: namaRuangan,
                    lantai: lantai
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);

                    // Reset modal
                    modal.querySelector('input').value = '';
                    modal.querySelector('select').selectedIndex = 0;

                    // Hide modal
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    bsModal.hide();

                    // Opsional: update list ruangan tanpa reload
                    // misal append ke select #roomSelect
                    const roomSelect = document.querySelector('#roomSelect');
                    if (roomSelect) {
                        const option = document.createElement('option');
                        option.value = data.room.id_room;
                        option.text = data.room.nama_ruangan;

                        roomSelect.appendChild(option);
                        roomSelect.value = data.room.id_room;
                    }

                } else {
                    alert('Gagal membuat ruangan');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan, coba lagi');
            });
        });
    });
    </script>
@endpush
