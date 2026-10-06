<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['message', 'link_text', 'link_url', 'starts_on', 'ends_on', 'is_active'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date', 'is_active' => 'boolean'];
    }

    /** The notice shown in the site-wide bar: time-limited notices win over open-ended ones. */
    public static function current(): ?self
    {
        $today = now(config('site.timezone'))->toDateString();

        return self::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_on')->orWhereDate('starts_on', '<=', $today))
            ->where(fn ($q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $today))
            ->orderByRaw('ends_on is null')
            ->latest('id')
            ->first();
    }

    public function isLive(): bool
    {
        return $this->is(self::current());
    }

    public function window(): string
    {
        return match (true) {
            $this->starts_on && $this->ends_on => $this->starts_on->format('j M').' – '.$this->ends_on->format('j M Y'),
            (bool) $this->ends_on => 'Until '.$this->ends_on->format('j M Y'),
            (bool) $this->starts_on => 'From '.$this->starts_on->format('j M Y'),
            default => 'No end date',
        };
    }
}
