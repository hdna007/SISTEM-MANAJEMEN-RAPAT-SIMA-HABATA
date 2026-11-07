<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'nama_lengkap' => 'rasyad',
            'username' => 'rasyad123',
            'email' => 'raisagadis2021@gmail.com',
            'password' => bcrypt('rasyad123'),
            'role' => 'user'
        ]);

        User::create([
            'nama_lengkap' => 'rizky',
            'username' => 'rizky123',
            'email' => 'rizky@example.com',
            'password' => bcrypt('rizky123'),
            'role' => 'user'
        ]);

        User::create([
            'nama_lengkap' => 'fatir',
            'username' => 'fatir123',
            'email' => 'fatir@example.com',
            'password' => bcrypt('fatir123'),
            'role' => 'user'
        ]);

        User::create([
            'nama_lengkap' => 'sandy',
            'username' => 'sandy123',
            'email' => 'sandysompel@gmail.com',
            'password' => bcrypt('sandy123'),
            'role' => 'user'
        ]);

        User::create([
            'nama_lengkap' => 'user',
            'username' => 'user123',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role' => 'user'
        ]);

        User::create([
            'nama_lengkap' => 'admin',
            'username' => 'admin123',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'nama_lengkap' => 'sekretaris',
            'username' => 'sekretaris123',
            'email' => 'sekretaris@example.com',
            'password' => bcrypt('sekretaris123'),
            'role' => 'sekretaris'
        ]);

        User::create([
            'nama_lengkap' => 'proktor',
            'username' => 'proktor123',
            'email' => 'proktor@example.com',
            'password' => bcrypt('proktor123'),
            'role' => 'proktor'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Aula (Lantai 5)',
            'kapasitas' => 25,
            'lokasi' => 'Lantai 5',
            'deskripsi' => 'Ruang Aula Besar',
            'is_active' => true,
            'gambar' => 'aula.jpg'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Garda (Lantai 5)',
            'kapasitas' => 50,
            'lokasi' => 'Lantai 5',
            'deskripsi' => 'Ruang Rapat Kecil',
            'is_active' => true,
            'gambar' => 'garda.jpg'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Karpet Merah (Lantai 5)',
            'kapasitas' => 75,
            'lokasi' => 'Lantai 5',
            'deskripsi' => 'Ruang rapat utama di lantai 5',
            'is_active' => true,
            'gambar' => 'karpet_merah.jpg'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Podcast (Lantai 5)',
            'kapasitas' => 100,
            'lokasi' => 'Lantai 5',
            'deskripsi' => 'Ruang rapat utama di lantai 5',
            'is_active' => true,
            'gambar' => 'podcast.jpg'
        ]);
        
        Room::create([
            'nama_ruangan' => 'Ruang Toba (Lantai 4)',
            'kapasitas' => 125,
            'lokasi' => 'Lantai 4',
            'deskripsi' => 'Ruang rapat utama di lantai 4',
            'is_active' => true,
            'gambar' => 'toba.jpg'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Working Space (Lantai 4)',
            'kapasitas' => 150,
            'lokasi' => 'Lantai 4',
            'deskripsi' => 'Ruang rapat utama di lantai 4',
            'is_active' => true,
            'gambar' => 'working_space.jpg'
        ]);

        Room::create([
            'nama_ruangan' => 'Ruang Command Center (Lantai 3)',
            'kapasitas' => 150,
            'lokasi' => 'Lantai 3',
            'deskripsi' => 'Ruang rapat utama di lantai 3',
            'is_active' => true,
            'gambar' => 'command_center.jpg'
        ]);

        Facility::create([
            'nama_fasilitas' => 'Proyektor',
            'deskripsi' => 'Fasilitas proyektor untuk presentasi',
            'is_active' => true
        ]);

        Facility::create([
            'nama_fasilitas' => 'Whiteboard',
            'deskripsi' => 'Fasilitas whiteboard untuk menulis',
            'is_active' => true
        ]);

        Facility::create([
            'nama_fasilitas' => 'WiFi',
            'deskripsi' => 'Fasilitas koneksi internet nirkabel',
            'is_active' => true
        ]);

        // Booking::create([
        //     'no_transaksi' => 'RPT-001',
        //     'id_room' => 1,
        //     'id_user' => 1,
        //     'judul_rapat' => 'Rapat tgl 5',
        //     'tanggal_mulai' => '2026-02-05 10:00:00',
        //     'tanggal_selesai' => '2026-02-05 11:00:00',
        //     'id_notulis' => 2,
        //     'status' => 'pending',
        //     'keterangan_tolak' => null
        // ]);
        
        // Booking::create([
        //     'no_transaksi' => 'RPT-002',
        //     'id_room' => 2,
        //     'id_user' => 2,
        //     'judul_rapat' => 'Rapat tgl 5',
        //     'tanggal_mulai' => '2026-02-05 14:00:00',
        //     'tanggal_selesai' => '2026-02-05 15:30:00',
        //     'id_notulis' => 3,
        //     'status' => 'pending',
        //     'keterangan_tolak' => null
        // ]);

        // Booking::create([
        //     'no_transaksi' => 'RPT-003',
        //     'id_room' => 2,
        //     'id_user' => 2,
        //     'judul_rapat' => 'Rapat tgl 5',
        //     'tanggal_mulai' => '2026-02-05 14:00:00',
        //     'tanggal_selesai' => '2026-02-05 15:30:00',
        //     'id_notulis' => 3,
        //     'status' => 'pending',
        //     'keterangan_tolak' => null
        // ]);

        // Booking::create([
        //     'no_transaksi' => 'RPT-004',
        //     'id_room' => 2,
        //     'id_user' => 2,
        //     'judul_rapat' => 'Rapat tgl 4',
        //     'tanggal_mulai' => '2026-02-04 21:00:00',
        //     'tanggal_selesai' => '2026-02-04 21:30:00',
        //     'id_notulis' => 3,
        //     'status' => 'pending',
        //     'keterangan_tolak' => null
        // ]);

    }
}
