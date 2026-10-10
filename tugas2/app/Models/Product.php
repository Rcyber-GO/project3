<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'name',
        'price',
        'stock',
        'image',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];
}
