<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_liburs';

    protected $fillable = [
        'nama_libur',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }
}