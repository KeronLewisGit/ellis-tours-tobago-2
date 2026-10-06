<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function publicPages(): array
    {
        return [
            'home' => ['/', 'Explore the greatest'],
            'tours' => ['/tours', 'Compare the tours.'],
            'rainforest walk' => ['/tours/3hr-rainforest-walk', 'Five waterfalls in the forest'],
            'boat tour' => ['/tours/buccoo-reef-nylon-pool', 'Standard rate'],
            'book' => ['/book', 'Which tour, and when?'],
            'about' => ['/about', 'Ellis Clarke'],
            'gallery' => ['/gallery', 'Rainforest Walks'],
            'reviews' => ['/reviews', 'Tell us about your day.'],
            'faq' => ['/faq', 'How do I pay?'],
            'contact' => ['/contact', 'ellisclarketourstt@gmail.com'],
            'search' => ['/search?q=hummingbird', '3-Hour Rainforest Walk'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_page_renders(string $url, string $expected): void
    {
        $this->get($url)->assertOk()->assertSee($expected);
    }

    public function test_unknown_page_returns_not_found(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee('We have wandered off the trail.');
    }

    public function test_unknown_tour_returns_not_found(): void
    {
        $this->get('/tours/moon-landing')->assertNotFound();
    }

    public static function oldAddresses(): array
    {
        return [
            'browse tours' => ['/tours-2', '/tours'],
            'tour page' => ['/tour-to-pirate-bay-full-day', '/tours/pirates-bay-full-day'],
            'booking page' => ['/book-half-day-island-tour-private', '/book?tour=half-day-island-tour&rate=private'],
        ];
    }

    #[DataProvider('oldAddresses')]
    public function test_old_wordpress_address_redirects_permanently(string $from, string $to): void
    {
        $this->get($from)->assertStatus(301)->assertRedirect($to);
    }

    public function test_unpublished_tour_is_hidden_from_visitors_but_previewable_by_staff(): void
    {
        $draft = Tour::factory()->unpublished()->create(['title' => 'Unreleased Night Walk']);

        $this->get('/tours')->assertDontSee('Unreleased Night Walk');
        $this->get("/tours/{$draft->slug}")->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->get("/tours/{$draft->slug}")
            ->assertOk()
            ->assertSee('Draft, visible to staff only');
    }

    public function test_tour_that_is_not_running_is_listed_but_cannot_be_booked(): void
    {
        $this->get('/tours')->assertSee('4×4 Off-Road Tour');
        $this->get('/tours/4x4-off-road-tour')->assertOk()->assertSee('Not running at the moment')->assertDontSee('Request to book');
        $this->get('/book')->assertDontSee('4×4 Off-Road Tour');

        $this->post('/enquiries/booking', $this->bookingData(['tour' => '4x4-off-road-tour']))
            ->assertSessionHasErrorsIn('booking', ['tour']);
    }

    public function test_tour_page_links_each_rate_to_its_online_payment_page(): void
    {
        $tour = Tour::where('slug', '3hr-rainforest-walk')->sole();

        $this->get('/tours/3hr-rainforest-walk')
            ->assertSee($tour->booking_group_url)
            ->assertSee($tour->booking_private_url)
            ->assertSee('US$130')
            ->assertSee('US$180');
    }

    public function test_date_checker_receives_upcoming_dates_without_staff_notes(): void
    {
        BookedDate::query()->delete();
        $date = now()->addMonths(3)->toDateString();
        BookedDate::create(['date' => $date, 'status' => 'booked', 'note' => 'Private charter for the Smith family']);
        BookedDate::create(['date' => now()->subMonth()->toDateString(), 'status' => 'booked']);

        $this->assertSame([$date => 'booked'], BookedDate::publicMap());
        $this->get('/')->assertSee($date)->assertDontSee('Smith family');
    }

    public function test_booking_request_is_stored_with_the_tour_rate_and_party_size(): void
    {
        $date = now()->addMonth()->toDateString();

        $this->from('/book')->post('/enquiries/booking', $this->bookingData([
            'rate' => 'private', 'tour_date' => $date, 'adults' => 2, 'children' => 1,
        ]))->assertRedirect('/book#form-booking')->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'guest@example.com')->sole();

        $this->assertSame('booking', $enquiry->type);
        $this->assertMatchesRegularExpression('/^BKG-\d{5}$/', $enquiry->reference);
        $this->assertSame('3-Hour Rainforest Walk · '.now()->addMonth()->format('j M Y').' · 3 guests', $enquiry->summary);
        $this->assertSame('3-Hour Rainforest Walk', $enquiry->data['tour']);
        $this->assertSame('Private tour (US$180 per person)', $enquiry->data['rate']);
        $this->assertSame($date, $enquiry->tourDate()->toDateString());
    }

    public function test_booking_for_a_single_price_tour_uses_the_standard_rate(): void
    {
        $this->post('/enquiries/booking', $this->bookingData(['tour' => 'buccoo-reef-nylon-pool', 'rate' => 'private', 'adults' => 1]))
            ->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'guest@example.com')->sole();

        $this->assertSame('Standard rate (US$60 per person)', $enquiry->data['rate']);
        $this->assertStringEndsWith('· 1 guest', $enquiry->summary);
    }

    public function test_booking_request_requires_a_tour_a_future_date_a_pickup_and_a_phone_number(): void
    {
        $this->post('/enquiries/booking', $this->bookingData([
            'tour' => '', 'tour_date' => now()->subDay()->toDateString(), 'pickup' => '', 'phone' => '', 'adults' => 0, 'payment' => 'Bitcoin',
        ]))->assertSessionHasErrorsIn('booking', ['tour', 'tour_date', 'pickup', 'phone', 'adults', 'payment']);

        $this->assertDatabaseMissing('enquiries', ['email' => 'guest@example.com']);
    }

    public function test_group_request_is_stored_with_the_group_size(): void
    {
        $this->post('/enquiries/group', [
            'name' => 'Teacher', 'email' => 'teacher@example.com', 'phone' => '868-555-0100',
            'group_type' => 'School or youth group', 'group_size' => 28, 'date_flexible' => '1',
            'interests' => ['Rainforest and waterfalls', 'Local food'],
        ])->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'teacher@example.com')->sole();

        $this->assertSame('School or youth group · 28 people', $enquiry->summary);
        $this->assertMatchesRegularExpression('/^GRP-\d{5}$/', $enquiry->reference);
        $this->assertSame('Yes', $enquiry->data['date_flexible']);
        $this->assertSame(['Rainforest and waterfalls', 'Local food'], $enquiry->data['interests']);
    }

    public function test_group_request_requires_at_least_one_interest_from_the_list(): void
    {
        $this->post('/enquiries/group', [
            'name' => 'Teacher', 'email' => 'teacher@example.com', 'phone' => '868-555-0100',
            'group_type' => 'School or youth group', 'group_size' => 1, 'interests' => ['Skydiving'],
        ])->assertSessionHasErrorsIn('group', ['group_size', 'interests.0']);
    }

    public function test_contact_form_stores_a_message(): void
    {
        $this->post('/enquiries/contact', [
            'name' => 'Visitor', 'email' => 'visitor@example.com', 'topic' => 'Boat tours', 'message' => 'Do you provide snorkelling gear?',
        ])->assertSessionHas('enquiry_success');

        $enquiry = Enquiry::where('email', 'visitor@example.com')->sole();

        $this->assertSame('Boat tours', $enquiry->summary);
        $this->assertMatchesRegularExpression('/^MSG-\d{5}$/', $enquiry->reference);
    }

    public function test_honeypot_submissions_are_silently_dropped(): void
    {
        $this->post('/enquiries/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Buy followers', 'website' => 'https://spam.example',
        ])->assertSessionHas('enquiry_success');

        $this->post('/reviews', [
            'name' => 'Bot', 'rating' => 5, 'body' => 'Great prices on followers, visit my site today.', 'website' => 'https://spam.example',
        ])->assertSessionHas('review_success');

        $this->assertDatabaseMissing('enquiries', ['email' => 'bot@example.com']);
        $this->assertDatabaseMissing('reviews', ['name' => 'Bot']);
    }

    public function test_unknown_enquiry_type_returns_not_found(): void
    {
        $this->post('/enquiries/mailbox', ['name' => 'Test'])->assertNotFound();
    }

    public function test_guest_review_waits_for_approval_before_it_is_shown(): void
    {
        $this->post('/reviews', [
            'name' => 'Marta', 'origin' => 'Germany', 'tour_title' => 'Half-Day Island Tour', 'rating' => 5,
            'body' => 'Ellis showed us corners of the island we would never have found alone.',
        ])->assertRedirect('/reviews#form-review')->assertSessionHas('review_success');

        $review = Review::where('name', 'Marta')->sole();

        $this->assertFalse($review->is_published);
        $this->assertSame('website', $review->source);
        $this->get('/reviews')->assertDontSee('corners of the island');

        $this->actingAs(User::factory()->create())->patch("/admin/reviews/{$review->id}")->assertSessionHas('status');

        $this->get('/reviews')->assertSee('corners of the island');
        $this->get('/tours/half-day-island-tour')->assertSee('corners of the island');
    }

    public function test_guest_review_requires_a_rating_and_a_real_tour(): void
    {
        $this->post('/reviews', ['name' => 'Marta', 'tour_title' => 'Helicopter Tour', 'rating' => 9, 'body' => 'Too short'])
            ->assertSessionHasErrorsIn('review', ['tour_title', 'rating', 'body']);
    }

    public function test_review_text_is_escaped_when_published(): void
    {
        Review::factory()->create(['body' => '<script>alert("x")</script> Lovely day out.']);

        $this->get('/reviews')->assertSee('Lovely day out.')->assertDontSee('<script>alert("x")</script>', false);
    }

    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'enquiries' => ['/admin/enquiries'],
            'tours' => ['/admin/tours'],
            'new tour' => ['/admin/tours/new'],
            'edit tour' => ['/admin/tours/3hr-rainforest-walk'],
            'calendar' => ['/admin/calendar'],
            'reviews' => ['/admin/reviews'],
            'announcements' => ['/admin/announcements'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_requires_sign_in_and_renders_for_staff(string $url): void
    {
        $this->get($url)->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_guests_cannot_change_tours_reviews_or_the_calendar(): void
    {
        $review = Review::factory()->pending()->create();

        $this->post('/admin/tours', ['title' => 'Free Tour'])->assertRedirect('/admin/login');
        $this->delete('/admin/tours/3hr-rainforest-walk')->assertRedirect('/admin/login');
        $this->patch("/admin/reviews/{$review->id}")->assertRedirect('/admin/login');
        $this->post('/admin/calendar', ['date' => now()->addWeek()->toDateString(), 'status' => 'booked'])->assertRedirect('/admin/login');

        $this->assertDatabaseHas('tours', ['slug' => '3hr-rainforest-walk']);
        $this->assertFalse($review->fresh()->is_published);
    }

    public function test_staff_can_sign_in_with_the_seeded_account_and_sign_out(): void
    {
        $this->post('/admin/login', ['email' => 'admin@ellistourstobago.com', 'password' => 'tobago-demo-2026'])
            ->assertRedirect('/admin');
        $this->assertAuthenticated();

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_sign_in_rejects_a_wrong_password(): void
    {
        $this->post('/admin/login', ['email' => 'admin@ellistourstobago.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_staff_can_filter_update_and_delete_enquiries(): void
    {
        $staff = User::factory()->create();
        $enquiry = Enquiry::factory()->create(['name' => 'Findable Guest']);

        $this->actingAs($staff)->get('/admin/enquiries?type=contact&q=Findable')->assertOk()->assertSee('Findable Guest');
        $this->actingAs($staff)->get('/admin/enquiries?type=booking&q=Findable')->assertDontSee('Findable Guest');

        $this->actingAs($staff)->patch("/admin/enquiries/{$enquiry->id}", ['status' => 'booked', 'notes' => 'Pickup at 8:30am'])
            ->assertSessionHas('status');
        $this->assertDatabaseHas('enquiries', ['id' => $enquiry->id, 'status' => 'booked', 'notes' => 'Pickup at 8:30am']);

        $this->actingAs($staff)->delete("/admin/enquiries/{$enquiry->id}")->assertRedirect('/admin/enquiries');
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }

    public function test_staff_can_block_a_booking_date_and_open_it_again(): void
    {
        BookedDate::query()->delete();
        $staff = User::factory()->create();
        $date = now()->addMonths(2)->toDateString();
        $enquiry = Enquiry::factory()->booking($date)->create();

        $this->actingAs($staff)->get("/admin/enquiries/{$enquiry->id}")->assertSee('Mark as fully booked');

        $this->actingAs($staff)->post('/admin/calendar', ['date' => $date, 'status' => 'limited'])->assertSessionHas('status');
        $this->actingAs($staff)->post('/admin/calendar', ['date' => $date, 'status' => 'booked'])->assertSessionHas('status');

        $this->assertSame([$date => 'booked'], BookedDate::publicMap());
        $this->actingAs($staff)->get("/admin/enquiries/{$enquiry->id}")->assertSee('fully booked')->assertDontSee('Mark as fully booked');

        $this->actingAs($staff)->delete('/admin/calendar/'.BookedDate::sole()->id)->assertSessionHas('status');
        $this->assertSame([], BookedDate::publicMap());
    }

    public function test_staff_can_add_edit_and_delete_a_tour_with_photos(): void
    {
        Storage::fake('uploads');
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/admin/tours', [
            'title' => 'Little Tobago Bird Tour', 'category' => 'boat', 'summary' => 'A boat trip to the bird sanctuary.',
            'price_group' => 120, 'includes' => "Hotel pickup at 9am\n\n  Glass-bottom boat  ", 'is_published' => '1', 'is_available' => '1',
            'cover_file' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            'photos' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.png')],
        ])->assertRedirect('/admin/tours/little-tobago-bird-tour');

        $tour = Tour::where('slug', 'little-tobago-bird-tour')->sole();

        $this->assertSame(['Hotel pickup at 9am', 'Glass-bottom boat'], $tour->includes);
        $this->assertSame([['key' => 'group', 'label' => 'Standard rate', 'price' => 120, 'url' => null]], $tour->rates());
        $this->assertCount(3, $tour->images());
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $tour->cover));
        $this->get('/tours/little-tobago-bird-tour')->assertOk()->assertSee('Glass-bottom boat');

        $removed = $tour->gallery[0];
        $this->actingAs($staff)->put('/admin/tours/little-tobago-bird-tour', [
            'title' => 'Little Tobago Bird Sanctuary', 'category' => 'boat', 'summary' => 'Updated summary.',
            'price_group' => 120, 'price_private' => 160, 'remove' => [$removed],
        ])->assertRedirect('/admin/tours/little-tobago-bird-tour');

        $tour->refresh();
        $this->assertSame('Little Tobago Bird Sanctuary', $tour->title);
        $this->assertSame(120, $tour->fromPrice());
        $this->assertCount(2, $tour->rates());
        $this->assertFalse($tour->is_published);
        $this->assertCount(2, $tour->images());
        Storage::disk('uploads')->assertMissing(str_replace('uploads/', '', $removed));

        $cover = $tour->cover;
        $this->actingAs($staff)->delete('/admin/tours/little-tobago-bird-tour')->assertRedirect('/admin/tours');
        $this->assertDatabaseMissing('tours', ['slug' => 'little-tobago-bird-tour']);
        Storage::disk('uploads')->assertMissing(str_replace('uploads/', '', $cover));
    }

    public function test_tour_rejects_non_image_uploads_and_unsafe_payment_links(): void
    {
        Storage::fake('uploads');

        $this->actingAs(User::factory()->create())->post('/admin/tours', [
            'title' => 'Bad Upload', 'category' => 'walks', 'summary' => 'Test',
            'cover_file' => UploadedFile::fake()->create('script.php', 10, 'application/x-php'),
            'booking_group_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors(['cover_file', 'booking_group_url']);

        $this->assertDatabaseMissing('tours', ['title' => 'Bad Upload']);
    }

    public function test_staff_can_add_hide_and_remove_a_review(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/admin/reviews', [
            'name' => 'Priya', 'origin' => 'Canada', 'rating' => 5, 'body' => 'The hummingbirds landed on my hand.',
            'source' => 'tripadvisor', 'source_url' => 'https://www.tripadvisor.com/ShowUserReviews-example',
        ])->assertSessionHas('status');

        $review = Review::where('name', 'Priya')->sole();
        $this->assertTrue($review->is_published);
        $this->get('/reviews')->assertSee('The hummingbirds landed on my hand.')->assertSee('Tripadvisor');

        $this->actingAs($staff)->patch("/admin/reviews/{$review->id}")->assertSessionHas('status');
        $this->get('/reviews')->assertDontSee('The hummingbirds landed on my hand.');

        $this->actingAs($staff)->delete("/admin/reviews/{$review->id}")->assertSessionHas('status');
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_time_limited_announcement_takes_priority_and_can_be_switched_off(): void
    {
        $staff = User::factory()->create();
        $this->get('/')->assertSee('Book your Tobago tour online, or pay in cash at pickup.');

        $this->actingAs($staff)->post('/admin/announcements', [
            'message' => 'Christmas week is filling up.', 'link_text' => 'Book now', 'link_url' => '/book',
            'starts_on' => now()->subDay()->toDateString(), 'ends_on' => now()->addWeek()->toDateString(),
        ])->assertSessionHas('status');

        $this->get('/')->assertSee('Christmas week is filling up.')->assertDontSee('Book your Tobago tour online, or pay in cash at pickup.');

        $this->actingAs($staff)->patch('/admin/announcements/'.Announcement::where('message', 'Christmas week is filling up.')->sole()->id);
        $this->get('/')->assertDontSee('Christmas week is filling up.')->assertSee('Book your Tobago tour online, or pay in cash at pickup.');
    }

    public function test_announcement_link_must_be_a_site_path_or_https(): void
    {
        $this->actingAs(User::factory()->create())->post('/admin/announcements', [
            'message' => 'Unsafe link', 'link_text' => 'Click', 'link_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('link_url');
    }

    /**
     * A valid booking request for the seeded 3-hour rainforest walk.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function bookingData(array $overrides = []): array
    {
        return [
            'tour' => '3hr-rainforest-walk',
            'rate' => 'group',
            'tour_date' => now()->addMonth()->toDateString(),
            'adults' => 2,
            'pickup' => 'Hotel in Crown Point',
            'payment' => 'Cash at pickup',
            'contact_method' => 'WhatsApp',
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'phone' => '+44 7700 900100',
            ...$overrides,
        ];
    }
}
