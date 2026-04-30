<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Bulletin extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image',
        'category_id',
        'published_at',
        'user_id',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image)) {
            if (Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }

            return asset('storage/'.$this->image);
        }

        $categoryName = Str::lower((string) optional($this->category)->name);

        if (str_contains($categoryName, 'forensik')) {
            return asset('images/bulletins/digital-forensics.svg');
        }

        return asset('images/bulletins/data-engineering.svg');
    }
}
