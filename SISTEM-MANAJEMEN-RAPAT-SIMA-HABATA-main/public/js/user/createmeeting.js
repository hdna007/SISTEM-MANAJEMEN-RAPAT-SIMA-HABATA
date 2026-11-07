document.addEventListener('DOMContentLoaded', function () {

    /* ================= MODAL SEARCH PESERTA ================= */

    const inputCari   = document.getElementById('inputCariPeserta');
    const tbodyModal  = document.getElementById('listPesertaModal');
    const rowsModal   = tbodyModal.querySelectorAll('.row-peserta');

    inputCari.addEventListener('input', function () {
        const val = this.value.toLowerCase();

        if (!val) {
            tbodyModal.classList.add('d-none');
            return;
        }

        tbodyModal.classList.remove('d-none');

        rowsModal.forEach(row => {
            const nama = row.dataset.nama;
            const user = row.dataset.username;

            row.style.display =
                nama.includes(val) || user.includes(val)
                    ? ''
                    : 'none';
        });
    });


    /* ================= PESERTA + ROLE ================= */

    const tablePeserta   = document.querySelector('#tablePeserta tbody');
    const toggleWrapper  = document.getElementById('togglePesertaWrapper');
    const toggleBtn      = document.getElementById('togglePeserta');

    let expanded = false;

    function updateVisiblePeserta() {
        const rows = tablePeserta.querySelectorAll('tr');

        rows.forEach((row, i) => {
            row.style.display = (!expanded && i >= 5) ? 'none' : '';
        });

        toggleWrapper.classList.toggle('d-none', rows.length <= 5);
        toggleBtn.textContent = expanded ? 'Sembunyikan' : 'Lihat lainnya';
    }


    /* ===== ADD PESERTA DENGAN ROLE ===== */
    document.addEventListener('click', function (e) {

        if (e.target.classList.contains('btn-add-peserta')) {

            const btn = e.target;
            const modalRow = btn.closest('tr');

            const id   = btn.dataset.id;
            const nama = btn.dataset.nama;
            const user = btn.dataset.username;

            const role = modalRow.querySelector('.role-select').value;

            // Cegah duplicate peserta
            if (document.getElementById('peserta-' + id)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peserta sudah ditambahkan',
                    text: 'Peserta ini sudah ada di dalam daftar rapat.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Cek role unik
            const existingRoles = Array.from(
                tablePeserta.querySelectorAll('input[name$="[role]"]')
            ).map(input => input.value);

            if (role === 'pemimpin_rapat' && existingRoles.includes('pemimpin_rapat')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pemimpin sudah ada',
                    text: 'Hanya boleh 1 pemimpin rapat.'
                });
                return;
            }

            const notulenCount = existingRoles.filter(r => r === 'notulen').length;

            if (role === 'notulen' && notulenCount >= 2) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Notulen sudah penuh',
                    text: 'Maksimal hanya 2 notulen.'
                });
                return;
            }

            const badgeColor =
                role === 'pemimpin_rapat'
                    ? 'danger'
                    : role === 'notulen'
                    ? 'info'
                    : 'secondary';

            const row = document.createElement('tr');
            row.id = 'peserta-' + id;

            row.innerHTML = `
                <td>
                    ${nama}
                    <input type="hidden" name="participants[${id}][id_user]" value="${id}">
                    <input type="hidden" name="participants[${id}][role]" value="${role}">
                </td>
                <td>${user}</td>
                <td>
                    <span class="badge bg-${badgeColor}">
                        ${role.replace('_',' ')}
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                        Hapus
                    </button>
                </td>
            `;

            tablePeserta.appendChild(row);

            const rowsArray = Array.from(tablePeserta.querySelectorAll('tr'));
            rowsArray.sort((a, b) => {
                const getPriority = (tr) => {
                    const roleInput = tr.querySelector('input[name$="[role]"]');
                    if (!roleInput) return 3;
                    const val = roleInput.value;
                    if (val === 'pemimpin_rapat') return 1;
                    if (val === 'notulen') return 2;
                    return 3;
                };
                return getPriority(a) - getPriority(b);
            });
            
            tablePeserta.innerHTML = '';
            rowsArray.forEach(r => tablePeserta.appendChild(r));

            updateVisiblePeserta();
        }

        if (e.target.classList.contains('btn-remove')) {
            e.target.closest('tr').remove();
            updateVisiblePeserta();
        }
    });


    toggleBtn.addEventListener('click', function () {
        expanded = !expanded;
        updateVisiblePeserta();
    });

    updateVisiblePeserta();


    /* ================= REKOMENDASI ================= */

    const btnWaktu     = document.getElementById('btnRekomendasiWaktu');
    const btnRuang     = document.getElementById('btnRekomendasiRuang');
    const mulaiInput   = document.getElementById('tanggal_mulai');
    const selesaiInput = document.getElementById('tanggal_selesai');

    if (btnWaktu && window.recommendationTime) {
        btnWaktu.addEventListener('click', function () {

            mulaiInput.value   = window.recommendationTime.start;
            selesaiInput.value = window.recommendationTime.end;

            Swal.fire({
                icon: 'success',
                title: 'Jadwal diganti',
                text: 'Waktu rapat diubah ke slot terdekat.',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    if (btnRuang && window.recommendationRoom) {
        btnRuang.addEventListener('click', function () {

            const selectRoom = document.querySelector('select[name="id_room"]');
            selectRoom.value = window.recommendationRoom.id;

            Swal.fire({
                icon: 'success',
                title: 'Ruangan diganti',
                text: 'Ruang rapat diganti ke ' + window.recommendationRoom.nama,
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    const form = document.getElementById('formMeeting');

    if (form) {
        form.addEventListener('submit', function (e) {

            const roles = Array.from(
                document.querySelectorAll('input[name$="[role]"]')
            ).map(input => input.value);

            if (!roles.includes('pemimpin_rapat')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pemimpin belum dipilih',
                    text: 'Minimal harus ada 1 Pemimpin Rapat'
                });
                return;
            }

            if (!roles.includes('notulen')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Notulen belum dipilih',
                    text: 'Minimal harus ada 1 Notulen'
                });
                return;
            }
        });
    }

});
