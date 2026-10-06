<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    public const SOURCES = [
        'website' => 'Website review',
        'instagram' => 'Shared on Instagram',
        'tripadvisor' => 'Tripadvisor',
    ];

    protected $fillable = ['name', 'origin', 'tour_title', 'rating', 'body', 'source', 'source_url', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    /** Reviews approved for the website, newest first. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->latest('id');
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? Str::headline($this->source);
    }
}
