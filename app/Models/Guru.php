<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    protected $fillable = ['user_id', 'nip', 'nama_lengkap', 'no_hp', 'foto'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelasWali()
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    public function absensiMapelDicatat()
    {
        return $this->hasMany(AbsensiMapel::class, 'dicatat_oleh');
    }
}
