<?php

namespace App\Models;

use App\Models\Facility;
use App\Models\Notulen;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'no_transaksi',
        'id_user',
        'id_room',
        'judul_rapat',
        'tanggal_mulai',
        'tanggal_selesai',
        'id_notulis',
        'status',
        'keterangan_tolak'
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
];


    // pembuat booking
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'id_room', 'id_room');
    }

    public function participants()
    {
        return $this->belongsToMany(
            User::class,
            'booking_participants',
            'id_booking',
            'id_user'
        )->withPivot('peran_peserta')->withTimestamps();
    }


    public function facilities()
    {
        return $this->belongsToMany(
            Facility::class,
            'booking_facilities',
            'id_booking',
            'id_facility'
        )->withPivot('jumlah')
        ->withTimestamps();
    }

    public function notulen()
    {
        return $this->hasOne(Notulen::class, 'id_booking');
    }

    public function notulis()
    {
        return $this->belongsTo(User::class, 'id_notulis', 'id_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function pemimpin()
    {
        return $this->participants()
            ->wherePivot('peran_peserta', 'pemimpin_rapat');
    }

    public function getStatusRapatAttribute()
    {
        $now = Carbon::now('Asia/Jakarta');

        // kalau sudah dibatalkan
        if ($this->status === 'canceled') {
            return 'canceled';
        }

        // hanya berlaku kalau sudah disetujui proktor
        if ($this->status === 'approved_proktor') {

            if ($now->lt($this->tanggal_mulai)) {
                return 'upcoming';
            }

            if ($now->between($this->tanggal_mulai, $this->tanggal_selesai)) {
                return 'ongoing';
            }

            if ($now->gt($this->tanggal_selesai)) {
                return 'completed';
            }
        }

        return $this->status;
    }  

    public function getStatusBadgeAttribute()
    {
        $now = Carbon::now('Asia/Jakarta');

        if ($this->status == 'canceled') {
            return '<span class="status-badge badge-cancel">Rapat Dibatalkan</span>';
        }

        if ($this->status == 'pending_admin' || $this->status == 'approved_admin') {
            return '<span class="status-badge badge-warning">Menunggu persetujuan</span>';
        }

        if ($this->status == 'rejected_admin' || $this->status == 'rejected_proktor') {
            return '<span class="status-badge badge-danger">Rapat Ditolak</span>';
        }

        if ($this->status == 'approved_proktor') {

            $mulai = Carbon::parse($this->tanggal_mulai);
            $selesai = Carbon::parse($this->tanggal_selesai);

            if ($now->lt($mulai)) {
                return '<span class="status-badge badge bg-primary">Mendatang</span>';
            }

            if ($now->between($mulai, $selesai)) {
                return '<span class="status-badge badge-success">Sedang Berlangsung</span>';
            }

            if ($now->gt($selesai)) {
                $user = auth()->user();
                $isNotulen = $user && $this->participants()
                    ->where('booking_participants.id_user', $user->id_user)
                    ->where('booking_participants.peran_peserta', 'notulen')
                    ->exists();

                if ($this->notulen) {
                    return '<span class="status-badge badge-success">Selesai (Notulen Tersedia)</span>';
                }
                return '<span class="status-badge badge-secondary">Selesai</span>';
            }
        }

        return '<span class="status-badge badge-dark">Status Tidak Diketahui</span>';
    }

    public function getCanIsiNotulenAttribute()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        $isNotulen = $this->participants()
            ->where('booking_participants.id_user', $user->id_user)
            ->where('booking_participants.peran_peserta', 'notulen')
            ->exists();

        $selesai = Carbon::parse($this->tanggal_selesai);

        return $this->status === 'approved_proktor'
            && now()->gt($selesai)
            && $isNotulen;
    }
};
