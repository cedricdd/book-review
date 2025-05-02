<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'like', "%$title%");
    }

    public function scopePopular(Builder $query, ?string $start = null, ?string $end = null): Builder
    {
        return $query->withCount([
            'reviews as popular_order' => fn($query) => $this->filterByDateRange($query, $start, $end),
        ])->orderBy('popular_order', 'desc');
    }

    public function scopeHighestRated(Builder $query, ?string $start = null, ?string $end = null): Builder
    {
        return $query->withAvg([
            'reviews as highest_order' => fn($query) => $this->filterByDateRange($query, $start, $end),
        ], 'rating')->orderBy('highest_order', 'desc');
    }

    private function filterByDateRange(Builder $query, ?string $start, ?string $end): Builder
    {
        return $query->when($start, fn($query) => $query->where('created_at', '>=', $start))
            ->when($end, fn($query) => $query->where('created_at', '<=', $end));
    }

    public function scopeMinReviews(Builder $query, int $min): Builder
    {
        return $query->withCount('reviews')->having('reviews_count', '>=', $min);
    }

    public function getRatingAttribute() {
        return round($this->reviews_avg_rating, 2);
    }

    public function scopeSetSorting(Builder $query, string $sorting): Builder
    {
        return match ($sorting) {
            'title' => $query->orderBy('title', 'asc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'author' => $query->join('authors', 'books.author_id', '=', 'authors.id')->orderBy('authors.name', 'asc')->orderBy('title', 'asc'),
            'popular_month' => $query->popular(now()->subMonth(), now())->orderBy('reviews_avg_rating', 'desc'),
            'popular_6_months' => $query->popular(now()->subMonths(6), now())->orderBy('reviews_avg_rating', 'desc'),
            'popular_all' => $query->popular()->orderBy('reviews_avg_rating', 'desc'),
            'ratings_month' => $query->highestRated(now()->subMonth(), now())->orderBy('reviews_count', 'desc'),
            'ratings_6_months' => $query->highestRated(now()->subMonths(6), now())->orderBy('reviews_count', 'desc'),
            'ratings_all' => $query->highestRated()->orderBy('reviews_count', 'desc'),
            default => $query,
        };
    }

    public function getCoverAttribute(): string
    {
        return preg_match('/covers\/.*/', $this->cover_image) ? asset('storage/' . $this->cover_image) : $this->cover_image;
    }
}
