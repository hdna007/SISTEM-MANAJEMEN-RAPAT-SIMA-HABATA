document.addEventListener('DOMContentLoaded', function () {

    const modalDetailEl = document.getElementById('modalViewUndangan');
    const modalDetail = modalDetailEl
        ? new bootstrap.Modal(modalDetailEl)
        : null;

    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('vJudul').textContent = this.dataset.judul;
            document.getElementById('vKapasitas').textContent = this.dataset.kapasitas + " orang";
            document.getElementById('vRuangan').textContent = this.dataset.ruangan;
            document.getElementById('vMulai').textContent = this.dataset.mulai;
            document.getElementById('vSelesai').textContent = this.dataset.selesai;
            document.getElementById('vID').textContent = this.dataset.id;
            document.getElementById('vFasilitas').textContent = this.dataset.fasilitas;

            const form = document.getElementById('formApproveProktor');
            if (form) {
                form.action = form.dataset.url.replace(':id', this.dataset.id);
            }

            modalDetail?.show();
        });
    });

    const deviceSearch = document.getElementById('deviceSearch');

    if (deviceSearch) {
        deviceSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const deviceCards = document.querySelectorAll('.device-card');

            deviceCards.forEach(card => {
                const name = card.dataset.name;
                card.style.display = name.includes(query) ? 'flex' : 'none';
            });
        });
    }

    document.addEventListener('change', function(e){

        if(e.target.classList.contains('device-toggle')){

            const toggle = e.target;
            const card = toggle.closest('.device-card');
            const id = toggle.dataset.id;
            const isActive = toggle.checked ? 1 : 0;

            if(toggle.checked){
                card.classList.add('active');
            }else{
                card.classList.remove('active');
            }

            fetch('/proktor/toggle-facility', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                },
                body:JSON.stringify({
                    id:id,
                    is_active:isActive
                })
            });

        }

    });

    const btnShowRoom = document.getElementById("btnShowRoom");
    const modalRoomContainer = document.getElementById("modalRoomContainer");
    const roomModalEl = document.getElementById("roomModal");
    const roomModal = roomModalEl ? new bootstrap.Modal(roomModalEl) : null;
    const selectedDateInput = document.getElementById("selectedDateValue");
    const tempSlider = document.getElementById("tempSlider");
    const valDisplay = document.getElementById("valDisplay");

    if (tempSlider && valDisplay) {
        tempSlider.addEventListener("input", function() {
            valDisplay.textContent = this.value;
        });
    }

    if (btnShowRoom) {
        btnShowRoom.addEventListener("click", function () {
            let url = this.dataset.url + "?date=" + (selectedDateInput ? selectedDateInput.value : "");
            if (tempSlider) {
                url += "&capacity=" + tempSlider.value;
            }
            fetchRooms(url);
        });
    }

    function fetchRooms(url) {
        if (!modalRoomContainer) return;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                modalRoomContainer.innerHTML = "";
                if (!data.length) {
                    modalRoomContainer.innerHTML = `<div class="col-12 text-center text-muted">Tidak ada ruangan</div>`;
                } else {
                    data.forEach(room => {

                        const activeTxt = room.is_active == 1 ? 'Aktif' : 'Nonaktif';
                        const activeClass = room.is_active == 1 ? 'btn-success' : 'btn-secondary';

                        const statusClass = room.is_available ? 'status-available' : 'status-busy';
                        const statusTxt = room.is_available ? 'Tersedia' : 'Sedang Dipakai';

                        modalRoomContainer.innerHTML += `
                        <div class="col-md-6 mb-4">
                            <div class="room-gallery-item">

                                <div class="room-img-container position-relative">
                                    <img src="assets/rooms/${room.gambar}" class="room-img">

                                    <button 
                                        class="btn btn-sm ${activeClass} position-absolute top-0 end-0 m-2 room-status-toggle shadow"
                                        data-id="${room.id_room}"
                                        data-active="${room.is_active}">
                                        ${activeTxt}
                                    </button>
                                </div>

                                <div class="p-3">
                                    <h6 class="fw-bold mb-1">${room.nama_ruangan}</h6>

                                    <p class="text-muted small mb-2">
                                        ${room.kapasitas} orang | ${room.lokasi ?? '-'}
                                    </p>

                                    <span class="badge ${room.is_available ? 'bg-success' : 'bg-danger'}">
                                        ${room.is_available ? 'Tersedia' : 'Sedang Dipakai'}
                                    </span>

                                </div>
                            </div>
                        </div>
                        `;
                    });
                }
                roomModal?.show();
            });
    }

let roomChanges = {};

document.addEventListener('click', function(e) {

    if (e.target.classList.contains('room-status-toggle')) {

        const btn = e.target;
        const id = btn.dataset.id;

        const current = parseInt(btn.dataset.active);
        const newActive = current === 1 ? 0 : 1;

        btn.dataset.active = newActive;

        btn.textContent = newActive ? "Aktif" : "Nonaktif";

        btn.className = `btn btn-sm ${newActive ? 'btn-success' : 'btn-secondary'} position-absolute top-0 end-0 m-2 room-status-toggle shadow`;

        // simpan perubahan sementara
        roomChanges[id] = newActive;

    }

});

    const openCalendarBtn = document.getElementById('openCalendar');
    const calendarOverlay = document.getElementById('calendarOverlay');
    const closeCalendarBtn = document.getElementById('closeCalendar');
    const calendarDays = document.getElementById('calendarDays');
    const monthYearTitle = document.getElementById('monthYearTitle');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let currentCalDate = new Date();
    if (selectedDateInput && selectedDateInput.value) {
        currentCalDate = new Date(selectedDateInput.value);
    }

    if (openCalendarBtn) {
        openCalendarBtn.addEventListener('click', () => {
            calendarOverlay.style.display = 'flex';
            renderCalendar();
        });
    }

    if (closeCalendarBtn) {
        closeCalendarBtn.addEventListener('click', () => {
            const activeDay = document.querySelector('.cal-day.active');
            if (activeDay) {
                const day = activeDay.dataset.day;
                const month = currentCalDate.getMonth() + 1;
                const year = currentCalDate.getFullYear();
                const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                window.location.href = `?date=${dateStr}`;
            }
        });
    }

    window.hideCalendar = function() {
        calendarOverlay.style.display = 'none';
    }

    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', () => {
            currentCalDate.setMonth(currentCalDate.getMonth() - 1);
            renderCalendar();
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', () => {
            currentCalDate.setMonth(currentCalDate.getMonth() + 1);
            renderCalendar();
        });
    }

    function renderCalendar() {
        if (!calendarDays) return;
        calendarDays.innerHTML = '';
        
        const year = currentCalDate.getFullYear();
        const month = currentCalDate.getMonth();
        
        monthYearTitle.textContent = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(currentCalDate);
        
        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        
        for (let i = 0; i < firstDay; i++) {
            const div = document.createElement('div');
            div.className = 'cal-day empty';
            calendarDays.appendChild(div);
        }
        
        const today = new Date();
        const selected = selectedDateInput ? new Date(selectedDateInput.value) : null;

        for (let i = 1; i <= lastDate; i++) {
            const div = document.createElement('div');
            div.className = 'cal-day';
            div.textContent = i;
            div.dataset.day = i;
            
            if (today.getDate() === i && today.getMonth() === month && today.getFullYear() === year) {
                div.classList.add('today');
            }
            
            if (selected && selected.getDate() === i && selected.getMonth() === month && selected.getFullYear() === year) {
                div.classList.add('active');
            }
            
            div.addEventListener('click', function() {
                document.querySelectorAll('.cal-day').forEach(d => d.classList.remove('active'));
                this.classList.add('active');
            });
            
            calendarDays.appendChild(div);
        }
    }




    const saveRoomBtn = document.getElementById('saveRoomChanges');

    if(saveRoomBtn){

        saveRoomBtn.addEventListener('click', function(){
            if(Object.keys(roomChanges).length === 0){
                alert('Tidak ada perubahan ruangan');
                return;
            }
            fetch('/proktor/update-rooms', {
                method: 'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    rooms: roomChanges
                })
            })
            .then(res => res.json())
            .then(res => {

                if(res.success){

                    alert('Perubahan ruangan berhasil disimpan');

                    location.reload();

                }else{

                    alert('Gagal menyimpan perubahan');

                }

            });

        });

    }

    const btnShowAdd = document.getElementById('btnShowAdd');
    const modalAdd = document.getElementById('modalAdd');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnSaveDevice = document.getElementById('btnSaveDevice');
    const devName = document.getElementById('devName');
    const devDescription = document.getElementById('devDescription');
    const deviceList = document.getElementById('deviceList');

    btnShowAdd?.addEventListener('click', () => {
        modalAdd.style.display = 'flex';

        setTimeout(() => {
            modalAdd.classList.add('show');
        }, 10);
    });
    btnCloseModal?.addEventListener('click', () => {
        modalAdd.classList.remove('show');

        setTimeout(() => {
            modalAdd.style.display = 'none';
        }, 300);
    });
    modalAdd?.addEventListener('click', function(e) {
        if (e.target === modalAdd) {
            modalAdd.classList.remove('show');
            modalAdd.style.display = 'none';
            devName.value = '';
        }
    });
    btnSaveDevice?.addEventListener('click', () => {

        const nama = devName.value.trim();
        const deskripsi = devDescription.value.trim();

        if(!nama) return alert('Nama fasilitas harus diisi');

        fetch("/proktor/add-facility", {
            method: 'POST',
            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                nama_fasilitas: nama,
                deskripsi: deskripsi
            })
        })
        .then(res => res.json())
        .then(res => {

            if(res.success){

                const f = res.facility;

                const div = document.createElement('div');
                div.className = 'device-card active';
                div.dataset.name = f.nama_fasilitas.toLowerCase();

                div.innerHTML = `
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-4 bg-white shadow-sm">
                            <i class='bx bx-plug fs-3'></i>
                        </div>
                        <div>
                            <p class="m-0 fw-bold">${f.nama_fasilitas}</p>
                            <small class="text-muted">${f.deskripsi ?? '-'}</small>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <label class="switch">
                            <input type="checkbox" class="device-toggle" data-id="${f.id_facility}" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                `;

                deviceList.appendChild(div);
                limitDevices();


                modalAdd.style.display = 'none';
                devName.value = '';
                devDescription.value = '';

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Fasilitas berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 7500
                });

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Fasilitas gagal ditambahkan'
            });

        }

        });

    });

    function limitDevices() {

        const devices = document.querySelectorAll('#deviceList .device-card');
        const btnToggle = document.getElementById('btnShowMore');

        devices.forEach((device, index) => {

            if(index >= 4){
                device.classList.add('hidden-device');
            }

        });


        if(btnToggle && devices.length > 4){
            btnToggle.classList.remove('d-none');
        }

    }
    limitDevices();

    let devicesOpened = false;

    document.getElementById('btnShowMore')?.addEventListener('click', function(){

        const hiddenDevices = document.querySelectorAll('#deviceList .device-card');

        if(!devicesOpened){

            hiddenDevices.forEach((device,index)=>{
                if(index >= 4){
                    device.classList.remove('hidden-device');
                }
            });

            this.textContent = "Tutup";
            devicesOpened = true;

        }else{

            hiddenDevices.forEach((device,index)=>{
                if(index >= 4){
                    device.classList.add('hidden-device');
                }
            });

            this.textContent = "Lainnya";
            devicesOpened = false;

        }

    });



});