<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = ['nisn', 'nama_lengkap', 'jenis_kelamin', 'no_hp_ortu', 'qr_code', 'card_number', 'fingerprint_id', 'foto'];

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'siswa_id');
    }

    public function absensiHarian()
    {
        return $this->hasMany(AbsensiHarian::class, 'siswa_id');
    }

    public function absensiMapel()
    {
        return $this->hasMany(AbsensiMapel::class, 'siswa_id');
    }

    public function suratIzin()
    {
        return $this->hasMany(SuratIzin::class, 'siswa_id');
    }

    public function orangTuaSiswa()
    {
        return $this->hasMany(OrangTuaSiswa::class, 'siswa_id');
    }

    public function orangTua()
    {
        return $this->belongsToMany(User::class, 'orang_tua_siswa', 'siswa_id', 'user_id')
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    public function notifikasiKehadiran()
    {
        return $this->hasMany(NotifikasiKehadiran::class, 'siswa_id');
    }
}
