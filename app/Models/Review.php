<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function (Review $review) use($event) {
                Cache::forget('book_reviews_' . $review->book_id);

                Log::info('Cache cleared for book_reviews_' . $review->book_id . ' on ' . $event);
            });
        }
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
