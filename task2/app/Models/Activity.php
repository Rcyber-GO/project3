<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    /**
     * status TIDAK ada di $fillable: status hanya boleh berubah lewat ActivityService
     * (publish/complete), bukan lewat mass assignment dari form umum.
     */
    protected $fillable = [
        'category_id',
        'code',
        'title',
        'description',
        'start_at',
        'end_at',
        'location',
        'capacity',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'capacity' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /* ---------- Query scope: dipakai ActivityController::index ---------- */

    public function scopeSearch($query, ?string $keyword)
    {
        return $query->when($keyword, function ($query, $keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
        });
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->when($categoryId, fn ($query, $id) => $query->where('category_id', $id));
    }

    public function scopeWithStatus($query, ?string $status)
    {
        // whitelist: nilai status di luar daftar diabaikan
        return $query->when(
            in_array($status, ['draft', 'published', 'completed'], true),
            fn ($query) => $query->where('status', $status)
        );
    }

    public function scopeSortByStart($query, ?string $direction)
    {
        // 'oldest' = terlama dulu; selain itu default terbaru dulu
        return $query->orderBy('start_at', $direction === 'oldest' ? 'asc' : 'desc');
    }
}
