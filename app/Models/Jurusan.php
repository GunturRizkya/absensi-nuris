<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = ['kode_jurusan', 'nama_jurusan'];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'jurusan_id');
    }

    public function mapel()
    {
        return $this->hasMany(MataPelajaran::class, 'jurusan_id');
    }
}
