<?php

namespace App\Models;

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

    protected static function booted(): void
    {
        static::updated(function (Review $review) { Cache::forget("book-" . $review->book_id); });
        static::deleted(function (Review $review) { Cache::forget("book-" . $review->book_id); });
        static::created(function (Review $review) { Cache::forget("book-" . $review->book_id); });
    }
}
