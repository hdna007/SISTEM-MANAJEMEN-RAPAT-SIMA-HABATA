<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingFacility extends Model
{
    use HasFactory;

    protected $table = 'booking_facilities';
    public $timestamps = false;

    protected $fillable = [
        'id_booking',
        'id_facility',
        'jumlah'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'id_facility');
    }
}
