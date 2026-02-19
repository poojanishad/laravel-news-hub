<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'author',
        'source',
        'category',
        'url',
        'image_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];


    public function scopeUserPreferences(
    Builder $query,
    ?string $sources,
    ?string $categories,
    ?string $authors,
    ?string $search,
    ?string $date
): Builder {
    return $query
        ->when($sources, fn ($q) =>
            $q->whereIn('source', array_map('trim', explode(',', $sources)))
        )
        ->when($categories, fn ($q) =>
            $q->whereIn('category', array_map('trim', explode(',', $categories)))
        )
        ->when($authors, fn ($q) =>
            $q->whereIn('author', array_map('trim', explode(',', $authors)))
        )
        ->when($search, fn ($q) =>
                $q->where(fn ($query) =>
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                )
        )
        ->when($date, fn ($q) =>
                $q->whereDate('published_at', $date)
        );
}

}