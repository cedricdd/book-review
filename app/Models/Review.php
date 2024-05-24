<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['review', 'rating', 'ip_address'];

    public function book() {
        return $this->belongsTo(Book::class);
    }

    public function scopeCheckIfExist(Builder $query, int $bookID, string $ip) {
        return $query->where('book_id', $bookID)->where('ip_address', $ip)->exists();
    }

    protected static function booted(): void
    {
        static::updated(function (Review $review) { Cache::forget("book-" . $review->book_id); });
        static::deleted(function (Review $review) { Cache::forget("book-" . $review->book_id); });
        static::created(function (Review $review) { Cache::forget("book-" . $review->book_id); });
    }
}
