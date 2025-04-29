<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function scopeSetSorting(Builder $query, string $sorting): Builder
    {
        return match($sorting) {
            'newest' => $query->latest(),
            'country' => $query->orderBy('country', 'asc')->orderBy('name', 'asc'),
            'book_count' => $query->withCount('books')->orderBy('books_count', 'desc')->orderBy('name', 'asc'),
            default => $query->orderBy('name', 'asc'),
        };
    }
}
