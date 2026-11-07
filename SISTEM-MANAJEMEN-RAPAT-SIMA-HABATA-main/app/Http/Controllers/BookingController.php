<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{

    
    public function data()
    {

        
        $userId = auth()->id();

        // Base query: semua rapat yang dia ikut (creator atau participant)
        $baseQuery = Booking::where(function ($q) use ($userId) {
            $q->where('bookings.id_user', $userId)
            ->orWhereHas('participants', function ($q) use ($userId) {
                $q->where('booking_participants.id_user', $userId);
            });
        });

        // Total rapat yang dia ikuti (tanpa status)
        $totalRapat = (clone $baseQuery)->count();

        // Total rapat hari ini
        $rapatHariIni = (clone $baseQuery)
            ->whereDate('tanggal_mulai', now('Asia/Jakarta')->toDateString())
            ->count();

        // List untuk tabel
        $rapatList = (clone $baseQuery)
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        return view('user.data-rapat', [
            'active' => 'data-rapat',
            'rapatList' => $rapatList,
            'totalRapat' => $totalRapat,
            'rapatHariIni' => $rapatHariIni
        ]);
    }

    public function editMeeting($no_transaksi)
    {
        $booking = Booking::with(['participants', 'facilities'])
                    ->where('no_transaksi', $no_transaksi)
                    ->firstOrFail();

        $active = 'data-rapat';                                                                                                                                                                                             
        $now = now('Asia/Jakarta');                                                                                                                                                                                         
        $ruang = Room::all()->map(function($room) use ($now, $booking) {                                                                                                                                                    
        $isUsedNow = Booking::where('id_room', $room->id_room)
            ->where('status', 'approved_proktor')
            ->where('id_booking', '!=', $booking->id_booking)
            ->where(function($q) use ($now) {
                $q->where('tanggal_mulai', '<=', $now)
                ->where('tanggal_selesai', '>=', $now);
            })
            ->exists();                                                                                                                                                                                                 
                                                                                                                                                                                                                            
            $room->is_used_now = $isUsedNow;                                                                                                                                                                                
            return $room;                                                                                                                                                                                                   
        });
        $peserta = User::all();
        $fasilitas = Facility::all();

        return view('user.createmeeting', compact(
            'booking',
            'ruang',
            'peserta',
            'fasilitas',
            'active'
        ));
    }


    public function updateMeeting(Request $request, $no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)->firstOrFail();

        $now = now('Asia/Jakarta');

        if ($now->gt($booking->tanggal_selesai)) {
            return back()->with('failed', 'Rapat sudah selesai dan tidak dapat diedit');
        }

        $validated = $request->validate([
            'judul_rapat' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'id_room' => 'required|exists:rooms,id_room',
        ]);


        $mulaiBaru = Carbon::parse($validated['tanggal_mulai']);
        $mulaiLama = Carbon::parse($booking->tanggal_mulai);
        $selesaiLama = Carbon::parse($booking->tanggal_selesai);

        $sedangBerlangsung = $now->between($mulaiLama, $selesaiLama);

        if (!$sedangBerlangsung && $mulaiBaru->lt($now)) {
            return back()
                ->with('failed', 'Tanggal mulai tidak boleh lebih awal dari waktu sekarang')
                ->withInput();
        }

        if ($booking->status === 'approved_proktor' && $sedangBerlangsung) {

            $validated['status'] = $booking->status;

        } else {

            $validated['status'] = 'pending_admin';
        }

        $booking->update($validated); 
        $syncData = [];

        if ($request->filled('participants')) {
            foreach ($request->participants as $participant) {

                if (
                    isset($participant['id_user']) &&
                    isset($participant['role']) &&
                    !empty($participant['role'])
                ) {
                    $syncData[$participant['id_user']] = [
                        'peran_peserta' => $participant['role']
                    ];
                }
            }
        }

        $roles = collect($request->participants)->pluck('role');

        if (!$roles->contains('pemimpin_rapat')) {
            return back()->with('failed', 'Minimal harus ada 1 Pemimpin Rapat')->withInput();
        }

        if (!$roles->contains('notulen')) {
            return back()->with('failed', 'Minimal harus ada 1 Notulen')->withInput();
        }

        $booking->participants()->sync($syncData);

        return redirect()
            ->route('user.data')
            ->with([
                'alert_type' => 'update',
                'success' => 'Rapat berhasil diperbarui'
            ]);
    }

    public function cancelMeeting($no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)->firstOrFail();

        if ($booking->status === 'canceled') {
            return redirect()
                ->route('user.data')
                ->with('failed', 'Rapat sudah dibatalkan sebelumnya');
        }

        $booking->update([
            'status' => 'canceled'
        ]);

        return redirect()
            ->route('user.data')
            ->with([
                'alert_type' => 'cancel',
                'success' => 'Rapat berhasil dibatalkan'
            ]);
    }

    public function destroy($no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)->firstOrFail();

        $booking->delete();

        return redirect()->route('user.data')->with('success', 'Data rapat berhasil dihapus.');
    }

    public function create(){
        return view('user.createmeeting',[
            'title' => 'Buat Rapat',
            'active' => 'data-rapat',
            'peserta' => User::all(),                                                                                                                                                                                       
            'ruang' => Room::all()->map(function($room) {                                                                                                                                                                   
                $now = now('Asia/Jakarta');                                                                                                                                                                                 
                $room->is_used_now = Booking::where('id_room', $room->id_room)
                    ->where('status', 'approved_proktor')
                    ->where(function($q) use ($now) {
                        $q->where('tanggal_mulai', '<=', $now)
                        ->where('tanggal_selesai', '>=', $now);
                    })
                    ->exists();                                                                                                                                                                                            
                return $room;                                                                                                                                                                                               
            }),
            'fasilitas' => Facility::all()
        ]);
    }
    
    public function storeMeeting(Request $request)
    {
        $validated = $request->validate([
            'judul_rapat'             => 'required|string|max:255',
            'tanggal_mulai'   => [
                'required',
                'date',
                'after_or_equal:now', 
            ],
            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],
            'id_room'                 => 'required|exists:rooms,id_room',
            'participants' => 'required|array|min:1',
            'participants.*.id_user' => 'exists:users,id_user',
            'participants.*.role' => 'required|in:pemimpin_rapat,notulen,peserta',
            'fasilitas'               => 'nullable|array',
            'fasilitas.*'             => 'exists:facilities,id_facility',
        ]);

        $roles = collect($validated['participants'])->pluck('role');


        if ($roles->filter(fn($r) => $r === 'pemimpin_rapat')->count() > 1) {
            return back()->with('failed', 'Pemimpin rapat hanya boleh 1')->withInput();
        }

        if ($roles->filter(fn($r) => $r === 'notulen')->count() > 2) {
            return back()->with('failed', 'Notulen hanya boleh 2')->withInput();
        }

        // 🔥 TAMBAHKAN INI (WAJIB ADA)
        if (!$roles->contains('pemimpin_rapat')) {
            return back()->with('failed', 'Minimal harus ada 1 Pemimpin Rapat')->withInput();
        }

        if (!$roles->contains('notulen')) {
            return back()->with('failed', 'Minimal harus ada 1 Notulen')->withInput();
        }



        $start   = Carbon::parse($validated['tanggal_mulai']);
        $end     = Carbon::parse($validated['tanggal_selesai']);
        $durasi  = $start->diffInMinutes($end);

        /* ================= CEK BENTROK ================= */
        $conflicts = Booking::where('id_room', $validated['id_room'])
            ->whereNotIn('status', ['canceled', 'rejected_admin', 'rejected_proktor'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_mulai', [$start, $end])
                ->orWhereBetween('tanggal_selesai', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('tanggal_mulai', '<=', $start)
                    ->where('tanggal_selesai', '>=', $end);
                });
            })
            ->orderBy('tanggal_selesai')
            ->get();

        /* ================= ADA BENTROK ================= */
        if ($conflicts->isNotEmpty()) {

            // Ambil konflik TERAKHIR yang paling dekat
            $lastConflict = $conflicts->last();

            // Buffer kecil supaya tidak mepet
            $bufferMinutes = 15;

            // Rekomendasi: setelah jadwal bentrok terakhir
            $bufferMinutes = 15;
            $recommendedStart = Carbon::parse($lastConflict->tanggal_selesai)
                                    ->addMinutes($bufferMinutes);

            $recommendedEnd = $recommendedStart->copy()->addMinutes($durasi);

            /* ===== CARI SLOT YANG BENAR2 KOSONG ===== */
            while (Booking::where('id_room', $validated['id_room'])
                    ->where(function ($q) use ($recommendedStart, $recommendedEnd) {
                        $q->whereBetween('tanggal_mulai', [$recommendedStart, $recommendedEnd])
                        ->orWhereBetween('tanggal_selesai', [$recommendedStart, $recommendedEnd])
                        ->orWhere(function ($q) use ($recommendedStart, $recommendedEnd) {
                            $q->where('tanggal_mulai', '<=', $recommendedStart)
                                ->where('tanggal_selesai', '>=', $recommendedEnd);
                        });
                    })
                    ->exists()) {

                // Geser 15 menit sampai kosong
                $recommendedStart->addMinutes($bufferMinutes);
                $recommendedEnd = $recommendedStart->copy()->addMinutes($durasi);
            }


            /* ================= CARI RUANGAN ALTERNATIF ================= */
        $alternativeRoom = Room::where('is_active', 1)
                    ->where('id_room', '!=', $validated['id_room'])
                    ->whereDoesntHave('bookings', function ($q) use ($start, $end) {
                        $q->where(function ($q) use ($start, $end) {
                            $q->whereBetween('tanggal_mulai', [$start, $end])
                            ->orWhereBetween('tanggal_selesai', [$start, $end])
                            ->orWhere(function ($q) use ($start, $end) {
                                $q->where('tanggal_mulai', '<=', $start)
                                    ->where('tanggal_selesai', '>=', $end);
                            });
                        });
                    })
                    ->first();

            
            return back()
                    ->withInput()
                    ->with('recommendation_time', [
                        'start' => $recommendedStart->format('Y-m-d\TH:i'),
                        'end'   => $recommendedEnd->format('Y-m-d\TH:i'),
                    ])
                    ->with('recommendation_room', $alternativeRoom ? [
                        'id'   => $alternativeRoom->id_room,
                        'nama' => $alternativeRoom->nama_ruangan,
                    ] : null)
                    ->with('failed', 'Jadwal bentrok. Silakan pilih rekomendasi waktu atau ruangan lain.');

                    }


        /* ================== SIMPAN ================== */
        DB::beginTransaction();

        try {

            $notulens = collect($validated['participants'])
                ->where('role', 'notulen')
                ->pluck('id_user')
                ->toArray();
            $booking = Booking::create([
                'no_transaksi'    => Str::uuid(),
                'judul_rapat'     => $validated['judul_rapat'],
                'tanggal_mulai'   => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'id_room'         => $validated['id_room'],
                'id_user'         => auth()->id(),
                'id_notulis'      => $notulens[0] ?? null, // notulen utama
                'status'          => 'pending_admin',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            foreach ($validated['participants'] as $p) {
                DB::table('booking_participants')->insert([
                    'id_booking' => $booking->id_booking,
                    'id_user' => $p['id_user'],
                    'peran_peserta' => $p['role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (!empty($validated['fasilitas'])) {
                foreach ($validated['fasilitas'] as $f) {
                    DB::table('booking_facilities')->insert([
                        'id_booking'  => $booking->id_booking,
                        'id_facility' => $f,
                        'jumlah'      => 1,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('user.data')
                ->with([
                    'alert_type' => 'create',
                    'success' => 'Rapat berhasil dibuat, menunggu persetujuan admin'
                ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('failed', 'Gagal membuat rapat')->withInput();
        }
    }
}
