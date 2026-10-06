<?php

namespace App\Models;

use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    public const RATES = [
        'group' => 'Group tour',
        'private' => 'Private tour',
    ];

    protected $fillable = [
        'slug', 'title', 'category', 'summary', 'body', 'duration', 'distance', 'pickup', 'dropoff',
        'price_group', 'price_private', 'includes', 'bring', 'notes', 'cover', 'gallery',
        'booking_group_url', 'booking_private_url', 'is_available', 'is_featured', 'is_published', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'bring' => 'array',
            'notes' => 'array',
            'gallery' => 'array',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Tours visible on the website, in the order set in the admin. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Published tours that can be booked right now. */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->published()->where('is_available', true);
    }

    public function categoryLabel(): string
    {
        return config("site.categories.{$this->category}.name", Str::headline($this->category));
    }

    /** Cover first, then the rest of the gallery, as public paths. */
    public function images(): array
    {
        return array_values(array_unique(array_filter([$this->cover, ...($this->gallery ?? [])])));
    }

    /** Images uploaded through the admin, as opposed to the ones shipped with the site. */
    public function uploadedImages(): array
    {
        return array_values(array_filter($this->images(), fn (string $path) => str_starts_with($path, 'uploads/')));
    }

    /** The lowest per-person price in US dollars, for "from" labels. */
    public function fromPrice(): ?int
    {
        $prices = array_filter([$this->price_group, $this->price_private]);

        return $prices ? min($prices) : null;
    }

    /**
     * The ways this tour is sold. A tour with one price has a single standard rate.
     *
     * @return list<array{key: string, label: string, price: int, url: ?string}>
     */
    public function rates(): array
    {
        $rates = [];

        if ($this->price_group) {
            $rates[] = [
                'key' => 'group',
                'label' => $this->price_private ? self::RATES['group'] : 'Standard rate',
                'price' => $this->price_group,
                'url' => $this->booking_group_url,
            ];
        }

        if ($this->price_private) {
            $rates[] = [
                'key' => 'private',
                'label' => self::RATES['private'],
                'price' => $this->price_private,
                'url' => $this->booking_private_url,
            ];
        }

        return $rates;
    }

    /** A list attribute as one item per line, the format the admin form edits. */
    public function linesText(string $attribute): string
    {
        return implode("\n", $this->{$attribute} ?? []);
    }

    /**
     * @return list<string>
     */
    public static function parseLines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $text))));
    }
}
