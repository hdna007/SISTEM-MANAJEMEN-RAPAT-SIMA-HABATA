document.addEventListener('DOMContentLoaded', function () {

    const modalDetail = document.getElementById('modalDetailRapat');

    if (modalDetail) {
        modalDetail.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            document.getElementById('dJudul').innerText = button.dataset.judul;
            document.getElementById('dTanggal').innerText = button.dataset.tanggal;
            document.getElementById('dWaktu').innerText = button.dataset.waktu;
            document.getElementById('dJumlahPeserta').innerText = button.dataset.jumlahPeserta;
            document.getElementById('dRuang').innerText = button.dataset.ruangan;
            document.getElementById('dPenanggungJawab').innerText = button.dataset.penanggungJawab;

            const fasilitasContainer = document.getElementById('dFasilitas');
            let fasilitasData = button.getAttribute('data-fasilitas');

            fasilitasContainer.innerHTML = '';

            if (fasilitasData && fasilitasData.trim() !== '') {

                let fasilitasArray = fasilitasData.split(',');

                fasilitasArray.forEach(item => {
                    let span = document.createElement('span');
                    span.className = 'badge bg-secondary me-1 mb-1';
                    span.innerText = item.trim();
                    fasilitasContainer.appendChild(span);
                });

            } else {
                fasilitasContainer.innerText = 'Tidak ada fasilitas';
            }
        });
    }

    const modalAlasan = document.getElementById('modalAlasan');

    if (modalAlasan) {
        modalAlasan.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const alasan = button.dataset.alasan;

            document.getElementById('isiAlasan').innerText =
                alasan ? alasan : 'Tidak ada alasan tersedia.';
        });
    }
    
    document.querySelectorAll(".btn-cancel-custom").forEach(function(button) {

        button.addEventListener("click", function () {

            let form = this.closest("form");

            Swal.fire({
                title: "Yakin mau membatalkan rapat?",
                text: "Rapat yang dibatalkan tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Batalkan!",
                cancelButtonText: "Tidak"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });
    });

    document.querySelectorAll(".btn-delete").forEach(function(button) {

        button.addEventListener("click", function () {

            let form = this.closest("form");

            Swal.fire({
                title: "Hapus data rapat?",
                text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });

    });

});