<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Satu kategori dapat memiliki banyak kegiatan.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Urutan standar untuk dropdown/daftar kategori, dipakai di beberapa tempat.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
