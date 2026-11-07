<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = ['jurusan_id', 'wali_kelas_id', 'nama_kelas', 'tingkat'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'kelas_id');
    }
}
