<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\RapatApprovedMail;
use Illuminate\Support\Facades\Mail;


class ProktorController extends Controller
{
    public function index(Request $request)
    {
        $active = 'proktor';

        $selectedDate = $request->date 
            ? Carbon::parse($request->date)
            : now();

        // 🔥 semua ruangan
        $totalRooms = Room::count();

        // 🔥 ruangan yang terpakai di tanggal tersebut
        $bookedRoomIds = Booking::where(function ($query) use ($selectedDate) {
                $query->whereDate('tanggal_mulai', '<=', $selectedDate)
                    ->whereDate('tanggal_selesai', '>=', $selectedDate);
            })
            ->whereIn('status', ['approved_admin','approved_proktor'])
            ->pluck('id_room');

        // 🔥 ruangan tersedia
        $availableRooms = Room::whereNotIn('id_room', $bookedRoomIds)->get();

        $rapatList = Booking::with(['room','user','facilities'])
            ->whereDate('tanggal_mulai', $selectedDate)
            ->where('status', 'approved_admin')
            ->get();

        $facilities = Facility::all();
        $allRooms = Room::all();

        return view('proktor.index', compact(
            'rapatList',
            'active',
            'totalRooms',
            'availableRooms',
            'selectedDate',
            'facilities',
            'allRooms'
        ));
    }

    public function approve($id)
    {
        $booking = Booking::where('no_transaksi', $id)->firstOrFail();

        if ($booking->status !== 'approved_admin') {
            return back()->with('error', 'Status booking tidak valid.');
        }

        $conflict = Booking::where('id_room', $booking->id_room)
            ->where('status', 'approved_proktor')
            ->where('id_booking', '!=', $booking->id_booking)
            ->where(function ($query) use ($booking) {
                $query->where('tanggal_mulai', '<', $booking->tanggal_selesai)
                    ->where('tanggal_selesai', '>', $booking->tanggal_mulai);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Ruangan sudah dibooking di waktu tersebut!');
        }

        $booking->update([
            'status' => 'approved_proktor'
        ]);

        // ini untuk email lek
        foreach ($booking->participants as $participant) {
            if ($participant->email) {
                Mail::to($participant->email)
                    ->send(new RapatApprovedMail($booking));
            }
        }

        return back()->with('success', 'Booking berhasil diapprove!');
    }


    public function reject(Request $request, $id)
    {
        $booking = Booking::where('no_transaksi', $id)->firstOrFail();

        $booking->update([
            'status' => 'rejected_proktor',
            'keterangan_tolak' => $request->keterangan
        ]);

        return back()->with('success', 'Booking berhasil ditolak!');
    }

    public function filterRoom(Request $request)
    {
        $capacity = $request->capacity ?? 1;
        $date = $request->date ?? now();

        $date = Carbon::parse($date);

        // ambil ruangan yang sedang dipakai pada tanggal itu
        $bookedRoomIds = Booking::where(function ($query) use ($date) {
                $query->whereDate('tanggal_mulai', '<=', $date)
                    ->whereDate('tanggal_selesai', '>=', $date);
            })
            ->whereIn('status', ['approved_admin','approved_proktor'])
            ->pluck('id_room')
            ->toArray();

        // filter kapasitas
        $rooms = Room::where('kapasitas', '>=', $capacity)->get();

        $rooms->transform(function ($room) use ($bookedRoomIds) {

            $room->is_available = !in_array($room->id_room, $bookedRoomIds);

            return $room;
        });

        return response()->json($rooms);
    }

    public function toggleFacility(Request $request)
    {
        $facility = Facility::find($request->id);
        if ($facility) {
            $facility->is_active = $request->is_active;
            $facility->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function toggleRoomStatus(Request $request)
    {
        $room = Room::find($request->id);
        if ($room) {
            $room->is_active = $request->is_active;
            $room->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function updateFacilities(Request $request)
{
    foreach ($request->facilities as $facilityData) {

        $facility = Facility::find($facilityData['id']);

        if ($facility) {

            $facility->is_active = $facilityData['is_active'];
            $facility->save();

        }
    }

    return response()->json([
        'success' => true
    ]);
}

    public function updateRooms(Request $request)
    {
        foreach ($request->rooms as $id => $status) {

            $room = Room::find($id);

            if ($room) {
                $room->is_active = $status;
                $room->save();
            }

        }

        return response()->json([
            'success' => true
        ]);
    }

    public function addFacility(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255'
        ]);

        $facility = Facility::create([
            'nama_fasilitas' => $request->nama_fasilitas,
            'deskripsi' => $request->deskripsi,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'facility' => $facility
        ]);
    }
}