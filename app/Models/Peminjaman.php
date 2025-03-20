<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';
    protected $fillable = [
        'peminjam_user_id',
        'petugas_user_id',
        'buku_id',
        'waktu_peminjaman',
        'durasi_peminjaman_in_days',
        'waktu_pengembalian',
        'total_keterlambatan_in_days',
        'total_denda'
    ];

    public function peminjam()
    {
        return $this->belongsTo(User::class, 'peminjam_user_id', 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_user_id', 'user_id');
    }
}
