<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory;
    use SoftDeletes;   // BR-09: penghapusan kegiatan = soft delete (deleted_at), bisa di-restore

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_COMPLETED,
    ];

    /**
     * status TIDAK ada di $fillable: hanya berubah lewat ActivityService.
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

    /* ---------- Query scope ---------- */

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
        return $query->when(
            in_array($status, self::STATUSES, true),
            fn ($query) => $query->where('status', $status)
        );
    }

    public function scopeSortByStart($query, ?string $direction)
    {
        return $query->orderBy('start_at', $direction === 'oldest' ? 'asc' : 'desc');
    }
}
