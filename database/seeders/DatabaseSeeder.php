<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** The company's online booking channel, taken from the booking pages on ellistourstobago.com. */
    protected const BOOKING_URL = 'https://widgets.bokun.io/online-sales/7626cdf4-9f0f-438b-9791-76ad369f56ee/experience/';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ellistourstobago.com'],
            ['name' => 'Ellis Tours Admin', 'password' => 'tobago-demo-2026'],
        );

        $this->tours();
        $this->reviews();
        $this->announcements();
        $this->sampleDates();
        $this->sampleEnquiries();
    }

    /** Tours, prices and photos carried over from the pages on ellistourstobago.com. */
    protected function tours(): void
    {
        $photo = fn (string $name) => "images/photos/{$name}.webp";

        foreach (json_decode(file_get_contents(database_path('data/tours.json')), true) as $index => $tour) {
            Tour::updateOrCreate(['slug' => $tour['slug']], [
                'title' => $tour['title'],
                'category' => $tour['category'],
                'summary' => $tour['summary'],
                'body' => $tour['body'],
                'duration' => $tour['duration'],
                'distance' => $tour['distance'],
                'pickup' => $tour['pickup'],
                'dropoff' => $tour['dropoff'],
                'price_group' => $tour['price_group'],
                'price_private' => $tour['price_private'],
                'includes' => $tour['includes'],
                'bring' => $tour['bring'],
                'notes' => $tour['notes'],
                'cover' => $photo($tour['cover']),
                'gallery' => array_map($photo, $tour['gallery']),
                'booking_group_url' => $tour['booking_group'] ? self::BOOKING_URL.$tour['booking_group'] : null,
                'booking_private_url' => $tour['booking_private'] ? self::BOOKING_URL.$tour['booking_private'] : null,
                'is_available' => $tour['is_available'],
                'is_featured' => $tour['is_featured'],
                'sort_order' => ($index + 1) * 10,
            ]);
        }
    }

    /** What guests and Tobago's tourism board said about the tours in public Instagram posts. */
    protected function reviews(): void
    {
        $reviews = [
            ['@tonyahk', 'Tobago', 'The tour was full of breathtaking views and Mr. Ellis is a passionate and knowledgeable guide who loves what he does! Our tour included pick-up and drop off, guided rainforest and waterfall hike, snacks and drinks after our hike, entrance fee to Shurland’s Nature Park, delicious local lunch and lifelong memories! I highly recommend this adventure for everyone; whether visiting or living in Tobago!', 'https://www.instagram.com/reel/DCabXOfRc8_/'],
            ['@islandtingtt', 'Trinidad and Tobago', 'I explored the oldest protected rainforest in the Western Hemisphere with Ellis Tours and I recommend you do too! The tour included stops at waterfalls (five to be exact), the chance to connect with hummingbirds and a delicious lunch overlooking the magnificent Englishman’s Bay. A day very well spent and I cannot wait to do it again!', 'https://www.instagram.com/reel/DBjhqbGxgkh/'],
            ['@visittobago', 'Visit Tobago', 'A hike through the classic Gilpin Trace trail in the Main Ridge Rainforest is always filled with surprises. With a number of waterfalls, insects, wildlife and bird sightings!', 'https://www.instagram.com/reel/DCmSZM7RMBV/'],
        ];

        foreach ($reviews as [$name, $origin, $body, $url]) {
            Review::updateOrCreate(['source_url' => $url], [
                'name' => $name,
                'origin' => $origin,
                'tour_title' => '3-Hour Rainforest Walk',
                'body' => $body,
                'source' => 'instagram',
                'is_published' => true,
            ]);
        }
    }

    protected function announcements(): void
    {
        Announcement::updateOrCreate(['message' => 'Book your Tobago tour online, or pay in cash at pickup.'], [
            'link_text' => 'Browse tours', 'link_url' => '/tours',
        ]);
    }

    /** Example dates so the date checker has something to show in a demo. */
    protected function sampleDates(): void
    {
        if (BookedDate::exists()) {
            return;
        }

        $saturday = now(config('site.timezone'))->next('Saturday');

        foreach ([[1, 'limited'], [2, 'booked'], [3, 'limited'], [5, 'booked'], [8, 'limited']] as [$weeks, $status]) {
            BookedDate::create([
                'date' => $saturday->copy()->addWeeks($weeks)->toDateString(),
                'status' => $status,
                'note' => 'Sample date for the demo',
            ]);
        }
    }

    /** Clearly-labelled sample submissions so the admin inbox is not empty in a demo. */
    protected function sampleEnquiries(): void
    {
        if (Enquiry::where('is_sample', true)->exists()) {
            return;
        }

        $tourDate = now(config('site.timezone'))->next('Tuesday')->addWeeks(4)->toDateString();
        $label = date('j M Y', strtotime($tourDate));

        $samples = [
            ['booking', 'Sample Guest A', 'sample.a@example.com', '+44 7700 900101', "3-Hour Rainforest Walk · {$label} · 2 guests", 'new', 2,
                ['tour' => '3-Hour Rainforest Walk', 'rate' => 'Private tour (US$180 per person)', 'tour_date' => $tourDate, 'adults' => 2, 'pickup' => 'Hotel in Crown Point', 'payment' => 'Cash at pickup', 'contact_method' => 'WhatsApp', 'heard' => 'Instagram', 'message' => 'We would love to feed the hummingbirds.']],
            ['booking', 'Sample Guest B', 'sample.b@example.com', '+1 416 555 0102', "Buccoo Reef & Nylon Pool · {$label} · 4 guests", 'in_progress', 20,
                ['tour' => 'Buccoo Reef & Nylon Pool', 'rate' => 'Standard rate (US$60 per person)', 'tour_date' => $tourDate, 'adults' => 2, 'children' => 2, 'pickup' => 'Villa in Mount Irvine', 'payment' => 'Pay online', 'contact_method' => 'Email', 'heard' => 'Tripadvisor']],
            ['group', 'Sample Teacher C', 'sample.c@example.com', '868-555-0103', 'School or youth group · 30 people', 'new', 30,
                ['group_type' => 'School or youth group', 'group_size' => 30, 'date_flexible' => 'Yes', 'interests' => ['Rainforest and waterfalls', 'Hummingbirds and wildlife', 'Local food'], 'message' => 'A one-day island and rainforest trip for a Form 4 geography class.']],
            ['group', 'Sample Guest D', 'sample.d@example.com', '+1 305 555 0104', 'Cruise ship visitors · 6 people', 'new', 52,
                ['group_type' => 'Cruise ship visitors', 'group_size' => 6, 'tour_date' => $tourDate, 'interests' => ['Island sightseeing', 'Buccoo Reef and Nylon Pool'], 'message' => 'We dock at 8am and need to be back on board by 4pm.']],
            ['contact', 'Sample Guest E', 'sample.e@example.com', null, 'Rainforest walks', 'new', 75,
                ['topic' => 'Rainforest walks', 'message' => 'Is the short walk suitable for a six-year-old?']],
            ['booking', 'Sample Guest F', 'sample.f@example.com', '+49 151 5550106', "Pirate’s Bay & Pink Sand Bay · {$label} · 2 guests", 'booked', 140,
                ['tour' => 'Pirate’s Bay & Pink Sand Bay', 'rate' => 'Group tour (US$140 per person)', 'tour_date' => $tourDate, 'adults' => 2, 'pickup' => 'Guesthouse in Castara', 'payment' => 'Cash at pickup', 'contact_method' => 'WhatsApp']],
        ];

        foreach ($samples as [$type, $name, $email, $phone, $summary, $status, $hoursAgo, $data]) {
            $enquiry = Enquiry::create([
                'type' => $type, 'name' => $name, 'email' => $email, 'phone' => $phone,
                'summary' => $summary, 'status' => $status, 'data' => $data, 'is_sample' => true,
            ]);
            $enquiry->forceFill(['created_at' => now()->subHours($hoursAgo), 'updated_at' => now()->subHours($hoursAgo)])->saveQuietly();
        }
    }
}
