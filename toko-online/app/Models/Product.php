<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'id_barang';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_barang',
        'nama_barang',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'stok' => 'integer',
        ];
    }
}
