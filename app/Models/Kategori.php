<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';
    protected $fillable = [
        'nama',
        'is_available'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
