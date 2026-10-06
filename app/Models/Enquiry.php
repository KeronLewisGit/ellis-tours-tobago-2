<?php

namespace App\Models;

use Database\Factories\EnquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Enquiry extends Model
{
    /** @use HasFactory<EnquiryFactory> */
    use HasFactory;

    public const TYPES = [
        'booking' => ['label' => 'Booking request', 'prefix' => 'BKG', 'icon' => 'ticket'],
        'group' => ['label' => 'Group or custom tour', 'prefix' => 'GRP', 'icon' => 'users'],
        'contact' => ['label' => 'Message', 'prefix' => 'MSG', 'icon' => 'message-square'],
    ];

    public const STATUSES = [
        'new' => 'New',
        'in_progress' => 'In progress',
        'booked' => 'Confirmed',
        'closed' => 'Closed',
    ];

    protected $fillable = [
        'type', 'reference', 'name', 'email', 'phone', 'summary', 'data', 'status', 'notes', 'is_sample',
    ];

    protected function casts(): array
    {
        return ['data' => 'array', 'is_sample' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::created(function (Enquiry $enquiry) {
            $prefix = self::TYPES[$enquiry->type]['prefix'] ?? 'ETT';
            $enquiry->updateQuietly(['reference' => sprintf('%s-%05d', $prefix, 1000 + $enquiry->id)]);
        });
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type]['label'] ?? Str::headline($this->type);
    }

    public function typeIcon(): string
    {
        return self::TYPES[$this->type]['icon'] ?? 'inbox';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? Str::headline($this->status);
    }

    /** The tour date the guest asked for, when the form collected one. */
    public function tourDate(): ?Carbon
    {
        $date = $this->data['tour_date'] ?? null;

        return $date ? Carbon::parse($date) : null;
    }

    /** A wa.me link for replying on WhatsApp, where most guests expect to hear back. */
    public function whatsappUrl(): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);

        if (strlen($digits) < 7) {
            return null;
        }

        // Local seven-digit numbers are Trinidad and Tobago numbers.
        return 'https://wa.me/'.(strlen($digits) === 7 ? '1868'.$digits : $digits);
    }

    /** Submitted fields as label => value pairs for display. */
    public function details(): array
    {
        return collect($this->data ?? [])
            ->reject(fn ($value) => $value === null || $value === '' || $value === [])
            ->mapWithKeys(fn ($value, $key) => [
                Str::of($key)->replace('_', ' ')->ucfirst()->toString() => is_array($value) ? implode(', ', $value) : $value,
            ])
            ->all();
    }
}
