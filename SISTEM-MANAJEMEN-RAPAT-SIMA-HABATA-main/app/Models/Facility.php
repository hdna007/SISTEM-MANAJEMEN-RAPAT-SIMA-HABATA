<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'id_facility';

    protected $fillable = [
        'nama_fasilitas',
        'deskripsi',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function bookingFacilities()
    {
        return $this->hasMany(BookingFacility::class, 'id_facility');
    }
}