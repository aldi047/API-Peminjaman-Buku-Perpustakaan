<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'buku_id';
    protected $fillable = [
        'kategori_id',
        'nama',
        'isbn',
        'pengarang',
        'sinopsis',
        'stok',
        'foto'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }
}
