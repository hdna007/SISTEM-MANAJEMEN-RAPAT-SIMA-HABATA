<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'role'
    ];

    protected $hidden = ['password'];

    // user membuat banyak booking
    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_participants',
            'id_user',
            'id_booking'
        )->withPivot('peran_peserta')
        ->withTimestamps();
    }

    // user sebagai peserta rapat
    public function bookingParticipants()
    {
        return $this->hasMany(BookingParticipant::class, 'id_user');
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class, 'id_user');
    }

    public function getDashboardRouteAttribute()
    {
        return match($this->role) {
            'admin' => route('admin.dashboard'),
            'sekretaris' => route('approver.dashboard'),
            default => route('user.dashboard'),
        };
    }

    public function notulen()
    {
        return $this->hasMany(Notulen::class, 'id_notulis', 'id_user');
    }
}
