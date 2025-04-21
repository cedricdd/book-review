<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'like', "%$title%");
    }

    public function scopePopular(Builder $query, string|null $start = null, string|null $end = null): Builder
    {
        return $query->withCount([
            'reviews' => fn($query) => $this->filterByDateRange($query, $start, $end),
        ])->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, string|null $start = null, string|null $end = null): Builder
    {
        return $query->withAvg([
            'reviews' => fn($query) => $this->filterByDateRange($query, $start, $end),
        ], 'rating')->orderBy('reviews_avg_rating', 'desc');
    }

    private function filterByDateRange(Builder $query, string|null $start, string|null $end): Builder
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
}
