<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notulen;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $timezone = 'Asia/Jakarta';
        $today = Carbon::now($timezone);

        $month = (int) $request->query('month', $today->month);
        $year  = (int) $request->query('year', $today->year);

        $selectedDate = $request->query('selected_date');

        $baseQuery = Booking::whereIn('status', ['pending_admin', 'approved_admin', 'approved_proktor'])
            ->whereMonth('tanggal_mulai', $month)
            ->whereYear('tanggal_mulai', $year);

        $tanggalList = $baseQuery
            ->pluck('tanggal_mulai')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->sort()
            ->values();

        if (!$request->has('selected_date') && $tanggalList->count() > 0) {
            return redirect()->route('admin.dashboard', [
                'month' => $month,
                'year' => $year,
                'selected_date' => $tanggalList->first()
            ]);
        }

        if (!$selectedDate && $tanggalList->count() > 0) {
            $selectedDate = $tanggalList->first();
        }

        $tanggalRapat = $tanggalList
            ->map(fn($date) => Carbon::parse($date)->day)
            ->unique()
            ->toArray();

        if ($selectedDate) {
            $rapatList = Booking::with(['room', 'user'])
                ->where('status', 'pending_admin')
                ->whereDate('tanggal_mulai', $selectedDate)
                ->latest('tanggal_mulai')
                ->get();
        } else {
            $rapatList = $baseQuery
                ->with(['room', 'user'])
                ->latest('tanggal_mulai')
                ->get();
        }

        $totalRapat = $rapatList->count();
        $timezone = 'Asia/Jakarta';
        $today = Carbon::now($timezone);

        $month = (int) $request->query('month', $today->month);
        $year  = (int) $request->query('year', $today->year);

        $selectedDate = $request->query('selected_date');


        $selectedDate = $request->query(
            'selected_date',
            Carbon::now($timezone)->format('Y-m-d')
        );

        if (empty($selectedDate)) {
            $selectedDate = $today->format('Y-m-d');
        }

        $baseQuery = Booking::where('status', 'pending_admin')
            ->whereMonth('tanggal_mulai', $month)
            ->whereYear('tanggal_mulai', $year);

        $tanggalRapat = $baseQuery
            ->pluck('tanggal_mulai')
            ->map(fn($date) => Carbon::parse($date)->day)
            ->unique()
            ->toArray();

        if ($selectedDate) {

            $rapatList = Booking::with(['room', 'user'])
                ->where('status', 'pending_admin')
                ->whereDate('tanggal_mulai', $selectedDate)
                ->latest('tanggal_mulai')
                ->get();

            $totalRapat = $rapatList->count(); // 🔥 total hari itu

        } else {

            $rapatList = $baseQuery
                ->with(['room', 'user'])
                ->latest('tanggal_mulai')
                ->get();

            $totalRapat = $rapatList->count(); // 🔥 total bulan itu
        }

        $roles = User::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $totalUser = $roles->sum();
        $adminCount = $roles['admin'] ?? 0;
        $userCount = $roles['user'] ?? 0;
        $proktorCount = $roles['proktor'] ?? 0;
        $approverCount = $roles['sekretaris'] ?? 0;
        
        $prevMonth = $month - 1 < 1 ? 12 : $month - 1;
        $prevYear  = $month - 1 < 1 ? $year - 1 : $year;

        $nextMonth = $month + 1 > 12 ? 1 : $month + 1;
        $nextYear  = $month + 1 > 12 ? $year + 1 : $year;

        $hasPrevMeeting = Booking::where('status', 'pending_admin')
            ->whereMonth('tanggal_mulai', $prevMonth)
            ->whereYear('tanggal_mulai', $prevYear)
            ->exists();

        $hasNextMeeting = Booking::where('status', 'pending_admin')
            ->whereMonth('tanggal_mulai', $nextMonth)
            ->whereYear('tanggal_mulai', $nextYear)
            ->exists();

        $chartData = Booking::selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_mulai', $year)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $chartLabels = [];
        $chartValues = [];

        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = Carbon::create()->month($i)->translatedFormat('M');
            $chartValues[] = $chartData[$i] ?? 0;
        }

        return view('admin.index', [
            'title' => 'Admin Dashboard',
            'active' => 'admin',
            'rapatList' => $rapatList,
            'tanggalRapat' => $tanggalRapat,
            'month' => $month,
            'year' => $year,
            'totalRapat' => $totalRapat,
            'totalUser' => $totalUser,
            'adminCount' => $adminCount,
            'userCount' => $userCount,
            'approverCount' => $approverCount,
            'proktorCount' => $proktorCount,
            'selectedDate' => $selectedDate,
            'hasPrevMeeting' => $hasPrevMeeting,
            'hasNextMeeting' => $hasNextMeeting,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }

    public function approveBooking($no_transaksi)
    {
        $booking = Booking::where('no_transaksi', $no_transaksi)->firstOrFail();

        if ($booking->status !== 'pending_admin') {
            return back()->with('failed', 'Status tidak valid');
        }

        $booking->update([
            'status' => 'approved_admin',
            'approved_by_admin' => auth()->id(),
            'approved_at_admin' => now()
        ]);

        return back()->with('success', 'Berhasil dikirim ke Proktor');
    }

    public function rejectBooking(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string'
        ]);

        $booking = Booking::where('no_transaksi', $id)->firstOrFail();

        if ($booking->status !== 'pending_admin') {
            return back()->with('failed', 'Status tidak valid');
        }

        $booking->update([
            'status' => 'rejected_admin',
            'keterangan_tolak' => $request->keterangan,
            'approved_by_admin' => auth()->id(),
            'approved_at_admin' => now()
        ]);

        return back()->with('success', 'Rapat berhasil ditolak');
    }

    public function arsipNotulen(Request $request)
    {
        $totalRapat = Booking::count();

        $rapatTerlaksana = Booking::where('status', 'approved_proktor')
            ->where('tanggal_selesai', '<', now('Asia/Jakarta'))
            ->count();

        $totalPeserta = DB::table('booking_participants')
            ->join('bookings', 'bookings.id_booking', '=', 'booking_participants.id_booking')
            ->where('bookings.status', 'approved_proktor')
            ->where('bookings.tanggal_selesai', '<', now('Asia/Jakarta'))
            ->distinct('booking_participants.id_user')
            ->count('booking_participants.id_user');

        $query = Booking::with(['room', 'user', 'notulen'])
            ->where('status', 'approved_proktor')
            ->where('tanggal_selesai', '<', now('Asia/Jakarta'));

        if ($request->filled('status')) {

            if ($request->status == 'selesai') {
                $query->where('status', 'approved_proktor')
                    ->where('tanggal_selesai', '<', now('Asia/Jakarta'));
            }

            if ($request->status == 'berlangsung') {
                $query->where('status', 'approved_proktor')
                    ->where('tanggal_mulai', '<=', now('Asia/Jakarta'))
                    ->where('tanggal_selesai', '>=', now('Asia/Jakarta'));
            }

            if ($request->status == 'menunggu') {
                $query->whereIn('status', [
                    'pending_admin',
                    'approved_admin'
                ]);
            }

            if ($request->status == 'approved') {
                $query->where('status', 'approved_proktor');
            }
        }

        if ($request->filled('search')) {
            $query->where('judul_rapat', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('tanggal_mulai', $request->date);
        }

        $notulenList = $query->latest('tanggal_mulai')->get();

        return view('admin.arsip-rapat', [
            'title' => 'Arsip Rapat',
            'active' => 'arsip-rapat',
            'notulenList' => $notulenList,
            'totalRapat' => $totalRapat,
            'rapatTerlaksana' => $rapatTerlaksana,
            'totalPeserta' => $totalPeserta
        ]);
    }

    // public function approveNotulen($id_notulen)
    // {
    //     $notulen = Notulen::where('id_notulen', $id_notulen)->firstOrFail();

    //     $notulen->update([
    //         'status' => 'approved'
    //     ]);

    //     return back()->with('success', 'Notulen berhasil disetujui');
    // }

    // public function rejectNotulen($id_notulen)
    // {
    //     $notulen = Notulen::where('id_notulen', $id_notulen)->firstOrFail();

    //     $notulen->update([
    //         'status' => 'rejected'
    //     ]);

    //     return back()->with('success', 'Notulen berhasil ditolak');
    // }

    // public function proktor()
    // {
    //     $proktorList = Booking::with(['room', 'user'])
    //         ->whereIn('status', ['approved_proktor', 'rejected_proktor']) // ✅ FIX DI SINI
    //         ->latest('tanggal_mulai')
    //         ->get();

    //     return view('admin.proktor', [
    //         'title' => 'Konfirmasi dari Proktor',
    //         'active' => 'proktor',
    //         'proktorList' => $proktorList
    //     ]);
    // }

    // public function kirimSekretaris($id)
    // {
    //     $booking = Booking::where('no_transaksi', $id)->firstOrFail();

    //     // Pastikan hanya yang sudah approved_admin yang bisa dikirim
    //     if ($booking->status !== 'approved_admin') {
    //         return back()->with('error', 'Status tidak valid untuk dikirim');
    //     }

    //     $booking->update([
    //         'status' => 'approved_', // 🔥 INI YANG KAMU MAU
    //     ]);

    //     return back()->with('success', 'Berhasil dikirim ke Sekretaris');
    // }

    // public function sekretaris()
    // {
    //     $sekretarisList = Booking::with(['room', 'user'])
    //         ->whereIn('status', [
    //             'approved_sekretaris',
    //             'rejected_sekretaris'
    //         ]) // 🔥 ambil dua-duanya
    //         ->latest('tanggal_mulai')
    //         ->get();

    //     return view('admin.sekretaris', [
    //         'title' => 'Konfirmasi Sekretaris',
    //         'active' => 'sekretaris',
    //         'sekretarisList' => $sekretarisList
    //     ]);
    // }
}