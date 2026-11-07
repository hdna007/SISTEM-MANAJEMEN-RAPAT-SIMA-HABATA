<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notulen extends Model
{
    use HasFactory;

    protected $table = 'notulen';
    protected $primaryKey = 'id_notulen';

    protected $fillable = [
        'id_booking',
        'judul_rapat',
        'tanggal_mulai',
        'tanggal_selesai',
        'pemimpin_rapat',
        'peserta',
        'id_notulis',
        'hasil_materi',
        'deskripsi_rapat',
        'dokumentasi_kegiatan',
        'pdf_file',
        'status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function notulis()
    {
        return $this->belongsTo(User::class, 'id_notulis');
    }

    public function getDokumentasiArrayAttribute()
    {
        if (empty($this->dokumentasi_kegiatan)) {
            return [];
        }

        $decoded = json_decode($this->dokumentasi_kegiatan, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return [$this->dokumentasi_kegiatan];
    }
}
