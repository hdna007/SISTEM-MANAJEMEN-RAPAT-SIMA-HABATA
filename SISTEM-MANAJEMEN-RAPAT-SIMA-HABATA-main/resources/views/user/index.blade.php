    @extends('layouts.app')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">
    @endpush
    @section('content')
    <div class="wrapper">

        <!--KALENDER-->
        <main class="content">
            <div class="fade-slide mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <button id="prevMonth" class="btn btn-sm btn-light border border-dark">&lt;</button>
                    <h4 class="fw-bold mb-0" id="monthYear"></h4>
                    <button id="nextMonth" class="btn btn-sm btn-light border border-dark">&gt;</button>
                </div>
                <div class="d-flex justify-content-start my-3">
                    <div class="d-flex align-items-center gap-3 fs-5">
                        <span class="status-dot bg-warning"></span>
                        <span class="me-2 small">Mendatang</span>
                        <span class="status-dot bg-success"></span>
                        <span class="me-2 small">Sedang berlangsung</span>
                        <span class="status-dot bg-primary"></span>
                        <span class="small">Sudah selesai</span>
                        <i class="bi bi-person" style="font-size:1rem;"></i> 
                        <span class="small">Rapat anda</span>
                    </div>

                </div>

                <div class="card border-0 shadow-sm mb-2">
                    <div class="table-responsive">
                        <table class="table table-bordered calendar-table mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>MINGGU</th>
                                    <th>SENIN</th>
                                    <th>SELASA</th>
                                    <th>RABU</th>
                                    <th>KAMIS</th>
                                    <th>JUMAT</th>
                                    <th>SABTU</th>
                                </tr>
                            </thead>
                            <tbody id="calendar-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="kartu fade-slide">
                <div class="card1">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="fw-bold">Rapat Mendatang</h4>

                            @if($rapatMendatang)
                            <form>
                                <label>Judul:
                                    <input type="text" readonly value="{{ $rapatMendatang->judul_rapat }}">
                                </label>

                                <label>Waktu:
                                    <input type="text" readonly
                                        value="{{ \Carbon\Carbon::parse($rapatMendatang->tanggal_mulai)->format('d M Y H:i') }}">
                                </label>

                                <label>Ruangan:
                                    <input type="text" readonly value="{{ $rapatMendatang->room->nama_ruangan ?? '-' }}">
                                </label>
                            </form>

                            <div class="tombol d-flex gap-2">
                                <button
                                    class="btn btn-detail btn-success btn-lihat-detail"
                                    data-title="{{ $rapatMendatang->judul_rapat }}"
                                    data-no="{{ $rapatMendatang->no_transaksi }}"
                                    data-start="{{ $rapatMendatang->tanggal_mulai }}"
                                    data-end="{{ $rapatMendatang->tanggal_selesai }}"
                                    data-notulis="{{ $rapatMendatang->notulis->nama_lengkap ?? '-' }}"
                                    data-creator="{{ $rapatMendatang->creator->nama_lengkap ?? '-' }}"
                                    data-ruangan="{{ $rapatMendatang->room->nama_ruangan ?? '-' }}"
                                    data-color="warning">
                                    Lihat Detail
                                </button>

                                <button class="btn btn-qr">Qr-Absen</button>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada rapat mendatang</p>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

        <!-- Modal Detail Jadwal -->
        <div class="modal fade" id="eventModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header text-white" id="modalHeader">
                        <h5 class="modal-title" id="modalTitle"></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="text-muted small d-block">No. Transaksi</label>
                            <span id="modalNoTransaksi" class="fw-bold text-primary"></span>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="mb-3">
                                <label class="text-muted small d-block">Ruangan </label>
                                <span id="modalRuangan" class="fw-bold text-primary"></span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">Mulai</label>
                                <span id="modalTanggalMulai" class="fw-semibold"></span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">Selesai</label>
                                <span id="modalTanggalSelesai" class="fw-semibold"></span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-0">
                            <div class="row">
                                <div class="col-6">
                                    <label class="text-muted small d-block">Notulis</label>
                                    <span id="modalNotulis" class="fw-semibold"></span>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small d-block">Penanggung jawab</label>
                                    <span id="modalCreator" class="fw-semibold"></span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3" id="modalPdfRow" style="display: none;">
                            <div class="col-6">
                                <label class="text-muted small d-block mb-3">Preview PDF</label>
                                <a id="modalPdfPreview" href="#" target="_blank" class="fw-semibold text-primary btn btn-outline-primary">
                                    <span class="bi bi-eye"> </span>Lihat PDF
                                </a>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block mb-3">Download PDF</label>
                                <a id="modalPdfDownload" href="#" download class="fw-semibold text-success btn btn-outline-success">
                                    <span class="bi bi-download"> </span>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
@endsection

@push('scripts')
<script>

    let currentMonth = {{ (int)$month }};
    let currentYear = {{ (int)$year }};

    const monthNames = [
    "Januari","Februari","Maret","April","Mei","Juni",
    "Juli","Agustus","September","Oktober","November","Desember"
    ];

    function formatTime(date){
        let d = new Date(date);
        return d.getHours() + ':' + String(d.getMinutes()).padStart(2,'0');
    }

    function truncateText(text, max = 25){
        if(!text) return '';
        return text.length > max ? text.slice(0,max) + '...' : text;
    }

    function buildBadge(b) {
        let start = formatTime(b.tanggal_mulai);
        
        // Pastikan data string aman dari karakter kutipan
        const safeTitle = (b.judul_rapat || '').replace(/"/g, '&quot;');
        const safeNotulis = (b.notulis || '').replace(/"/g, '&quot;');
        const safeRuangan = (b.ruangan || '').replace(/"/g, '&quot;');
        const safeCreator = (b.creator || '').replace(/"/g, '&quot;');

        return `
        <div class="badge bg-${b.color} text-truncate event-badge mb-1 d-flex align-items-center gap-1"
            style="cursor:pointer"
            data-title="${safeTitle}"
            data-no="${b.no_transaksi}"
            data-start="${b.tanggal_mulai}"
            data-end="${b.tanggal_selesai}"
            data-notulis="${safeNotulis}"
            data-ruangan="${safeRuangan}"
            data-creator="${safeCreator}"
            data-color="${b.color}"
            data-pdf="${b.pdf_file || '#'}"
            data-pdf-download="${b.pdf_download || '#'}"
        >
            ${truncateText(`${start} (${b.judul_rapat})`)}
            ${b.is_participant ? '<i class="bi bi-person-fill text-light" style="font-size:0.75rem;"></i>' : ''}
        </div>
        `;
    }

    function loadCalendar(month, year){

    document.getElementById('monthYear').innerText = monthNames[month-1] + ' ' + year;

    fetch(`{{ route('calendar.data') }}?month=${month}&year=${year}`)
    .then(res => res.json())
    .then(bookings => {

    let firstDay = new Date(year, month-1, 1).getDay();
    let daysInMonth = new Date(year, month, 0).getDate();

    let tbody = '';
    let date = 1;

    for(let i=0;i<6;i++){

        tbody += '<tr>';

        for(let j=0;j<7;j++){

            if(i===0 && j<firstDay){
                tbody += '<td></td>';
            }
            else if(date>daysInMonth){
                tbody += '<td></td>';
            }
            else{

                let dayBookings = bookings
                .filter(b=>{
                    let d = new Date(b.tanggal_mulai);
                    return d.getDate()===date &&
                    (d.getMonth()+1)===month &&
                    d.getFullYear()===year;
                })
                .sort((a,b)=>new Date(a.tanggal_mulai)-new Date(b.tanggal_mulai));

                let maxVisible = window.innerWidth < 768 ? 1 : 2;

                let content = `
                <div class="fw-semibold mb-1">${date}</div>
                <div class="calendar-events">
                `;

                dayBookings.slice(0,maxVisible).forEach(b=>{
                    content += buildBadge(b);
                });

                if(dayBookings.length > maxVisible){

                    let hidden = dayBookings.slice(maxVisible)
                        .map(b => buildBadge(b))
                        .join('');

                    let popId = `popover-${year}-${month}-${date}`;

                    content += `
                    <div class="badge bg-light text-dark mt-1 more-events"
                        style="cursor:pointer"
                        data-pop="${popId}">
                        +${dayBookings.length-maxVisible} lainnya
                    </div>

                    <div id="${popId}" class="d-none">
                        ${hidden}
                    </div>
                    `;
                }

                content += '</div>';

                tbody += `<td class="calendar-day">${content}</td>`;

                date++;

            }

        }

        tbody+='</tr>';

    }

    document.getElementById('calendar-body').innerHTML = tbody;


    /* INIT POPOVER */
    document.querySelectorAll('.more-events').forEach(el=>{

        let popId = el.dataset.pop;
        let html = document.getElementById(popId).innerHTML;

        new bootstrap.Popover(el,{
            trigger:'click',
            placement: window.innerWidth < 768 ? 'bottom':'right',
            html:true,
            content: html,
            container:'body',   
            sanitize: false

        });

    });

    });

    }

/* MONTH NAVIGATION */

    document.getElementById('prevMonth').onclick = ()=>{
    currentMonth--;
    if(currentMonth<1){currentMonth=12;currentYear--;}
    loadCalendar(currentMonth,currentYear);
    };

    document.getElementById('nextMonth').onclick = ()=>{
    currentMonth++;
    if(currentMonth>12){currentMonth=1;currentYear++;}
    loadCalendar(currentMonth,currentYear);
    };


    /* BADGE CLICK → MODAL */

    document.addEventListener('click', function (e) {
        // Cari elemen badge terdekat dari yang diklik
        const badge = e.target.closest('.event-badge');
        
        // Jika tidak ketemu, atau klik di area kosong, abaikan
        if (!badge) return;

        e.stopPropagation();

        // Sembunyikan semua popover yang mungkin sedang terbuka
        document.querySelectorAll('.more-events').forEach(pop => {
            let inst = bootstrap.Popover.getInstance(pop);
            if (inst) inst.hide();
        });

        // Ambil data dari dataset
        const ds = badge.dataset;

        // Isi modal dengan data
        document.getElementById('modalTitle').innerText = ds.title || '-';
        document.getElementById('modalNoTransaksi').innerText = ds.no || '-';
        document.getElementById('modalTanggalMulai').innerText = ds.start || '-';
        document.getElementById('modalTanggalSelesai').innerText = ds.end || '-';
        document.getElementById('modalNotulis').innerText = ds.notulis || '-';
        document.getElementById('modalCreator').innerText = ds.creator || '-';
        document.getElementById('modalRuangan').innerText = ds.ruangan || '-';

        const pdf = ds.pdf;
        const pdfDownload = ds.pdfDownload;

        const previewBtn = document.getElementById('modalPdfPreview');
        const downloadBtn = document.getElementById('modalPdfDownload');

        if (previewBtn) previewBtn.href = (pdf && pdf !== '#') ? pdf : '#';
        if (downloadBtn) downloadBtn.href = (pdfDownload && pdfDownload !== '#') ? pdfDownload : '#';

        document.getElementById('modalPdfRow').style.display = (pdf && pdf !== '#') ? 'flex' : 'none';

        const header = document.getElementById('modalHeader');
        if (header) {
            header.className = 'modal-header text-white bg-' + (ds.color || 'primary');
        }

        // Tampilkan modal
        const myModal = new bootstrap.Modal(document.getElementById('eventModal'));
        myModal.show();
    });


    /* BUTTON DETAIL */

    document.addEventListener('click',function(e){

    const btn = e.target.closest('.btn-lihat-detail');
    if(!btn) return;

    e.stopPropagation();

    document.getElementById('modalTitle').innerText = btn.dataset.title;
    document.getElementById('modalNoTransaksi').innerText = btn.dataset.no;
    document.getElementById('modalTanggalMulai').innerText = btn.dataset.start;
    document.getElementById('modalTanggalSelesai').innerText = btn.dataset.end;
    document.getElementById('modalNotulis').innerText = btn.dataset.notulis;
    document.getElementById('modalCreator').innerText = btn.dataset.creator;
    document.getElementById('modalRuangan').innerText = btn.dataset.ruangan;

    document.getElementById('modalPdfRow').style.display = 'none';

    const header = document.getElementById('modalHeader');
    header.className = 'modal-header text-white bg-'+btn.dataset.color;

    new bootstrap.Modal(document.getElementById('eventModal')).show();

    });


    loadCalendar(currentMonth,currentYear);

</script>
@endpush