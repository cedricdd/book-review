<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query, $title) {
        $query->where("title", "like", "%". $title ."%");
    }

    public function scopePopular(Builder $query, string $from = null, string $to = null) {
        $query->withCount(['reviews' => fn(Builder $queryReview) => $this->dateRangeBuilder($queryReview, $from, $to)])->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, string $from = null, string $to = null) {
        $query->withAvg(['reviews' => fn(Builder $queryReview) => $this->dateRangeBuilder($queryReview, $from, $to)], 'rating')->orderBy('reviews_avg_rating','desc');
    }

    public function scopeMinReviews(Builder $query, int $min) {
        $query->having('reviews_count', '>=', $min);
    }

    private function dateRangeBuilder(Builder $query, string $from = null, string $to = null) {
        if(is_null($from) && !is_null($to)) {
            $query->where('created_at', '<=', $to);
        } elseif(!is_null($from) && is_null($to)) {
            $query->where('created_at', '>=', $from);
        } elseif(!is_null($from) && !is_null($to)) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }
}
