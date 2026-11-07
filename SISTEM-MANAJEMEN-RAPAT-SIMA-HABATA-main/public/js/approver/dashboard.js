document.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const modalId = event.target.id;

    // =========================
    // Modal Detail
    // =========================
    if (modalId === 'modalDetail') {

        document.getElementById('detailJudul').innerText =
            button.getAttribute('data-judul');

        document.getElementById('detailTanggal').innerText =
            button.getAttribute('data-tanggal');

        document.getElementById('detailPJ').innerText =
            button.getAttribute('data-pj');

        document.getElementById('detailRuangan').innerText =
            button.getAttribute('data-ruangan');

        // ✅ Tambahan baru
        document.getElementById('detailJumlah').innerText =
            button.getAttribute('data-jumlah');

        document.getElementById('detailFasilitas').innerText =
            button.getAttribute('data-fasilitas');
    }

    // =========================
    // Modal Tolak
    // =========================
    if (modalId === 'modalTolak') {
        const id = button.getAttribute('data-id');

        document.getElementById('formTolak')
            .action = `/approver/${id}/reject`;
    }
});
