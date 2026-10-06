<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BookedDate extends Model
{
    public const STATUSES = [
        'booked' => 'Fully booked',
        'limited' => 'Limited availability',
    ];

    protected $fillable = ['date', 'status', 'note'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', now(config('site.timezone'))->toDateString())->orderBy('date');
    }

    /**
     * Upcoming dates for the public date checker, without the internal notes.
     *
     * @return array<string, string> Y-m-d => status
     */
    public static function publicMap(): array
    {
        return self::upcoming()->get()
            ->mapWithKeys(fn (BookedDate $day) => [$day->date->toDateString() => $day->status])
            ->all();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Booked';
    }
}
