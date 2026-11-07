<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'lantai'       => 'required|integer|min:1|max:10',
        ]);

        $room = Room::create([
            'nama_ruangan' => $validated['nama_ruangan'],
            'lokasi'       => 'Lantai ' . $validated['lantai'],
            'is_active'    => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil dibuat',
            'room'    => $room,
        ]);
    }
}