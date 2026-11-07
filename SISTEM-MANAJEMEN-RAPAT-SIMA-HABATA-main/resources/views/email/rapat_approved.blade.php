<div style="font-family: Arial, sans-serif; background-color:#f4f6f9; padding:30px;">
    
    <div style="max-width:600px; margin:auto; background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);">

        <!-- Header -->
        <div style="background-color:#53CBF3; padding:20px; color:white; text-align:center;">
            <h2 style="margin:0;">📢 Undangan Rapat</h2>
            <p style="margin:5px 0 0 0;">Sistem Manajemen Rapat - SIMA HABATA</p>
        </div>

        <!-- Body -->
        <div style="padding:25px; color:#333;">
            
            <p>Halo Peserta Rapat,</p>

            <p>
                Anda telah diundang untuk mengikuti rapat yang telah <strong>disetujui</strong>. 
                Berikut detail rapat yang perlu Anda hadiri:
            </p>

            <table style="width:100%; border-collapse:collapse; margin-top:15px;">
                <tr>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><strong>Judul Rapat</strong></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">{{ $booking->judul_rapat }}</td>
                </tr>
                <tr>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><strong>Peran Anda</strong></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">
                        {{ $booking->participants->where('user_id', auth()->id())->first()->role ?? 'Peserta' }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><strong>Ruangan</strong></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">{{ $booking->room->nama_ruangan ?? '-' }}</td>
                </tr>

                <tr>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><strong>Waktu</strong></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">
                        {{ \Carbon\Carbon::parse($booking->tanggal_mulai)->format('d M Y H:i') }}
                    </td>
                </tr>
            </table>

            <!-- Reminder Box -->
            <div style="margin-top:20px; padding:15px; background:#fff3cd; border-left:5px solid #ffc107; border-radius:5px;">
                ⏰ <strong>Mohon hadir tepat waktu</strong> agar rapat dapat berjalan dengan lancar.
            </div>

            <!-- Button -->
            <div style="text-align:center; margin-top:25px;">
                <a href="#" 
                   style="background:#4e73df; color:white; padding:12px 25px; text-decoration:none; border-radius:5px; font-weight:bold;">
                   Lihat Detail Rapat
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div style="background:#f8f9fc; text-align:center; padding:15px; font-size:13px; color:#777;">
            Email ini dikirim otomatis oleh sistem <strong>SIMA HABATA</strong> <br>
            © {{ date('Y') }} Sistem Manajemen Rapat
        </div>

    </div>

</div>