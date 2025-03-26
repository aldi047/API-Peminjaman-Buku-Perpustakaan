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

    // protected $appends = [
    //     'peminjam',
    //     'petugas',
    //     'buku'
    // ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'peminjam_user_id',
        'petugas_user_id',
        'buku_id'
    ];

    protected $casts = [
        'waktu_peminjaman' => 'datetime',
        'waktu_pengembalian' => 'datetime'
    ];

    public function detailPeminjam()
    {
        return $this->belongsTo(User::class, 'peminjam_user_id', 'user_id');
    }

    public function detailPetugas()
    {
        return $this->belongsTo(User::class, 'petugas_user_id', 'user_id');
    }

    public function detailBuku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'buku_id');
    }

    // public function getPeminjamAttribute()
    // {
    //     return $this->namaPeminjam->nama;
    // }

    // public function getPetugasAttribute()
    // {
    //     return $this->namaPetugas->nama;
    // }

    // public function getBukuAttribute()
    // {
    //     return $this->namaBuku->nama;
    // }
}
