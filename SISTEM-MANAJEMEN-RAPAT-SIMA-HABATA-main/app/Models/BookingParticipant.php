<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingParticipant extends Model
{
    use HasFactory;

    protected $table = 'booking_participants';
    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'id_user',
        'peran_peserta'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
