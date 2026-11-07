<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notulen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotulenController extends Controller
{
    private function generateAndSavePdf($booking, $notulen)
    {
        $pdf = Pdf::loadView('notulen.notulen-pdf', compact('booking','notulen'))
                ->setPaper('a4','portrait');

        $pdfFileName = 'notulen_'.$booking->no_transaksi.'.pdf';
        $folderPath = storage_path('app/public/notulen_pdf');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $pdf->save($folderPath.'/'.$pdfFileName);

        $notulen->update([
            'pdf_file' => 'notulen_pdf/'.$pdfFileName
        ]);
    }

    public function notulen()
    {
        $active = 'notulen';
        $user = auth()->user();

        if (!$user || !$user->participants()->where('peran_peserta', 'notulen')->exists()) {
            abort(403, 'Anda tidak memiliki akses sebagai notulen.');
        }

        $rapatList = Booking::whereHas('participants', function ($q) use ($user) {
                $q->where('booking_participants.id_user', $user->id_user)
                ->where('booking_participants.peran_peserta', 'notulen');
            })
            ->with(['room', 'user', 'participants', 'notulen'])
            ->latest()
            ->get()
            ->filter(function ($booking) {
                return $booking->status_rapat === 'completed';
            });

        return view('notulen.data-notulen', compact('rapatList', 'active'));
    }

    public function showNotulen($no_transaksi)
    {
        $active = 'notulen';

        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['notulen', 'participants', 'room', 'notulis', 'pemimpin'])
            ->firstOrFail();

        // Kalau belum ada notulen → arahkan ke form create
        if (!$booking->notulen) {
            return redirect()->route('notulen.create', $no_transaksi);
        }

        // Kalau sudah ada notulen → tampilkan detail
        return view('notulen.detail-notulen', [
            'booking' => $booking,
            'notulen' => $booking->notulen,
            'active' => $active
        ]);
    }

    public function createNotulen($no_transaksi)
    {
        $active = 'notulen';

        $booking = Booking::where('no_transaksi', $no_transaksi)->firstOrFail();
        if (!$booking->can_isi_notulen) {
            abort(403, 'Anda tidak memiliki akses sebagai notulen untuk rapat ini.');
        }

        if (Notulen::whereHas('booking', function ($q) use ($no_transaksi) {
            $q->where('no_transaksi', $no_transaksi);
        })->exists()) {
            return redirect()->route('notulen.edit', $no_transaksi);
        }

        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['user','participants', 'pemimpin', 'notulis'])
            ->firstOrFail();
        
        return view('notulen.form-notulen', [
            'booking' => $booking,
            'active' => $active
        ]);
    }


    public function storeNotulen(Request $request, $no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['participants','pemimpin'])
            ->firstOrFail();

        $request->validate([
            'hasil_materi' => 'required|string',
            'deskripsi_rapat' => 'required|string',
            'dokumentasi_kegiatan' => 'required|array|max:4',
            'dokumentasi_kegiatan.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120'
        ], [
            'dokumentasi_kegiatan.max' => 'Maksimal 4 foto/file yang dapat diunggah.'
        ]);

        $filePaths = [];
        if ($request->hasFile('dokumentasi_kegiatan')) {
            foreach ($request->file('dokumentasi_kegiatan') as $file) {
                $filePaths[] = $file->store('dokumentasi_notulen', 'public');
            }
        }

        $notulen = Notulen::create([
            'id_booking' => $booking->id_booking,
            'judul_rapat' => $booking->judul_rapat,
            'tanggal_mulai' => $booking->tanggal_mulai,
            'tanggal_selesai' => $booking->tanggal_selesai,
            'pemimpin_rapat' => optional($booking->pemimpin()->first())->nama_lengkap ?? '-',
            'peserta' => $booking->participants
                ->where('pivot.peran_peserta','peserta')
                ->pluck('nama_lengkap')
                ->implode(', '),
            'id_notulis' => $request->id_notulis,
            'hasil_materi' => $request->hasil_materi,
            'deskripsi_rapat' => $request->deskripsi_rapat,
            'dokumentasi_kegiatan' => json_encode($filePaths)
        ]);

        $this->generateAndSavePdf($booking, $notulen);

        return redirect()
            ->route('notulen.index')
            ->with('success', 'Notulen berhasil disimpan dan PDF dibuat.');
    }

    public function editNotulen($no_transaksi)
    {
        $active = 'notulen';

        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with('notulen')
            ->firstOrFail();

        if (!$booking->notulen) {
            return redirect()->route('notulen.create', $no_transaksi);
        }

        if (!$booking->can_isi_notulen) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit notulen ini.');
        }

        return view('notulen.form-notulen', [
            'booking' => $booking,
            'notulen' => $booking->notulen,
            'active' => $active
        ]);
    }

    public function updateNotulen(Request $request, $no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['participants','pemimpin','notulen'])
            ->firstOrFail();

        if (!$booking->can_isi_notulen) {
            abort(403, 'Anda tidak memiliki izin untuk memperbarui notulen ini.');
        }

        $notulen = $booking->notulen;

        $request->validate([
            'hasil_materi' => 'required|string',
            'deskripsi_rapat' => 'required|string',
            'dokumentasi_kegiatan' => 'nullable|array|max:4',
            'dokumentasi_kegiatan.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120'
        ], [
            'dokumentasi_kegiatan.max' => 'Maksimal 4 foto/file yang dapat diunggah.'
        ]);

        $existingImages = $request->input('existing_images', []);
        $filePaths = [];
        
        if (is_array($existingImages)) {
            $filePaths = $existingImages;
        }

        if ($request->hasFile('dokumentasi_kegiatan')) {
            foreach ($request->file('dokumentasi_kegiatan') as $file) {
                $filePaths[] = $file->store('dokumentasi_notulen', 'public');
            }
        }

        if (count($filePaths) > 4) {
            return back()->with('error', 'Total maksimal foto adalah 4.');
        }

        $notulen->dokumentasi_kegiatan = json_encode($filePaths);

        $notulen->update([
            'hasil_materi' => $request->hasil_materi,
            'deskripsi_rapat' => $request->deskripsi_rapat
        ]);

        $this->generateAndSavePdf($booking, $notulen);

        return redirect()
            ->route('notulen.index')
            ->with('success', 'Notulen berhasil diperbarui dan PDF diperbarui.');
    }

    public function previewPdf($no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['notulen','participants','pemimpin'])
            ->firstOrFail();

        if (!$booking->notulen) {
            return back()->with('failed','Notulen belum tersedia.');
        }

        $notulen = $booking->notulen;

        // Tambahkan opsi 'html_input' => 'strip' untuk keamanan
        $config = [
            'renderer' => [
                'soft_break' => "<br>\n",
            ],
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $notulen->deskripsi_rapat_html = Str::markdown($notulen->deskripsi_rapat ?? '', $config);
        $notulen->hasil_materi_html = Str::markdown($notulen->hasil_materi ?? '', $config);

        $pdf = Pdf::loadView('notulen.notulen-pdf', [
            'booking' => $booking,
            'notulen' => $notulen
        ])->setPaper('a4','portrait');

        return $pdf->stream('Notulen-'.$booking->no_transaksi.'.pdf');
    }

    public function downloadPdf($no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)
            ->with(['notulen','participants','pemimpin'])
            ->firstOrFail();

        if (!$booking->notulen) {
            return back()->with('failed','Notulen belum tersedia.');
        }

        $pdf = Pdf::loadView('notulen.notulen-pdf', [
            'booking' => $booking,
            'notulen' => $booking->notulen
        ])->setPaper('a4','portrait');

        return $pdf->download('Notulen-'.$booking->no_transaksi.'.pdf');
    }
}
