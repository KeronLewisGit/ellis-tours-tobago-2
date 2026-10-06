<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\BookedDate;
use App\Models\Enquiry;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        return Auth::check() ? redirect()->route('admin.dashboard') : view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Those details do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): View
    {
        $counts = Enquiry::selectRaw('type, status, count(*) as total')->groupBy('type', 'status')->get();

        return view('admin.dashboard', [
            'byType' => collect(Enquiry::TYPES)->map(fn ($meta, $type) => [
                'meta' => $meta,
                'total' => $counts->where('type', $type)->sum('total'),
                'new' => $counts->where('type', $type)->where('status', 'new')->sum('total'),
            ]),
            'open' => $counts->whereIn('status', ['new', 'in_progress'])->sum('total'),
            'newCount' => $counts->where('status', 'new')->sum('total'),
            'thisWeek' => Enquiry::where('created_at', '>=', now()->subDays(7))->count(),
            'recent' => Enquiry::latest()->take(8)->get(),
            'dates' => BookedDate::upcoming()->take(5)->get(),
            'pendingReviews' => Review::where('is_published', false)->count(),
        ]);
    }

    public function enquiries(Request $request): View
    {
        $enquiries = Enquiry::query()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('summary', 'like', "%{$term}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.enquiries', ['enquiries' => $enquiries]);
    }

    public function enquiry(Enquiry $enquiry): View
    {
        $tourDate = $enquiry->tourDate();

        return view('admin.enquiry', [
            'enquiry' => $enquiry,
            'tourDate' => $tourDate,
            'bookedDate' => $tourDate ? BookedDate::whereDate('date', $tourDate)->first() : null,
        ]);
    }

    public function updateEnquiry(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $enquiry->update($request->validate([
            'status' => ['required', Rule::in(array_keys(Enquiry::STATUSES))],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]));

        return back()->with('status', 'Enquiry updated.');
    }

    public function destroyEnquiry(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries')->with('status', "Enquiry {$enquiry->reference} deleted.");
    }

    public function tours(): View
    {
        return view('admin.tours', ['tours' => Tour::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function createTour(): View
    {
        return view('admin.tour', ['tour' => new Tour(['is_published' => true, 'is_available' => true])]);
    }

    public function storeTour(Request $request): RedirectResponse
    {
        $tour = new Tour;
        $this->saveTour($request, $tour);

        return redirect()->route('admin.tours.edit', $tour)->with('status', 'Tour added.');
    }

    public function editTour(Tour $tour): View
    {
        return view('admin.tour', ['tour' => $tour]);
    }

    public function updateTour(Request $request, Tour $tour): RedirectResponse
    {
        $this->saveTour($request, $tour);

        return redirect()->route('admin.tours.edit', $tour)->with('status', 'Tour saved.');
    }

    public function destroyTour(Tour $tour): RedirectResponse
    {
        Storage::disk('uploads')->delete(array_map(fn (string $path) => Str::after($path, 'uploads/'), $tour->uploadedImages()));
        $tour->delete();

        return redirect()->route('admin.tours')->with('status', "“{$tour->title}” removed.");
    }

    public function dates(): View
    {
        return view('admin.dates', [
            'upcoming' => BookedDate::upcoming()->get(),
            'past' => BookedDate::whereDate('date', '<', now(config('site.timezone'))->toDateString())->orderByDesc('date')->take(10)->get(),
        ]);
    }

    public function storeDate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(array_keys(BookedDate::STATUSES))],
            'note' => ['nullable', 'string', 'max:160'],
        ]);

        // Matched with whereDate because SQLite stores the date with a time component.
        $day = BookedDate::whereDate('date', $data['date'])->first() ?? new BookedDate;
        $day->fill(['date' => $data['date'], 'status' => $data['status'], 'note' => $data['note'] ?? null])->save();

        return back()->with('status', 'Calendar updated. The date checker on the website now reflects it.');
    }

    public function destroyDate(BookedDate $date): RedirectResponse
    {
        $date->delete();

        return back()->with('status', 'Date opened up again.');
    }

    public function reviews(): View
    {
        return view('admin.reviews', [
            'reviews' => Review::orderBy('is_published')->latest('id')->get(),
            'tours' => Tour::orderBy('sort_order')->pluck('title')->all(),
        ]);
    }

    /** Add a review received somewhere else, such as Tripadvisor or a WhatsApp message. */
    public function storeReview(Request $request): RedirectResponse
    {
        Review::create([...$request->validate([
            'name' => ['required', 'string', 'max:80'],
            'origin' => ['nullable', 'string', 'max:80'],
            'tour_title' => ['nullable', 'string', 'max:120'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'max:1500'],
            'source' => ['required', Rule::in(array_keys(Review::SOURCES))],
            'source_url' => ['nullable', 'url', 'starts_with:https://', 'max:255'],
        ]), 'is_published' => true]);

        return back()->with('status', 'Review published.');
    }

    public function toggleReview(Review $review): RedirectResponse
    {
        $review->update(['is_published' => ! $review->is_published]);

        return back()->with('status', $review->is_published ? 'Review published on the website.' : 'Review hidden.');
    }

    public function destroyReview(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review removed.');
    }

    public function announcements(): View
    {
        return view('admin.announcements', [
            'announcements' => Announcement::latest('id')->get(),
            'live' => Announcement::current(),
        ]);
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        Announcement::create($request->validate([
            'message' => ['required', 'string', 'max:140'],
            'link_text' => ['nullable', 'string', 'max:40', 'required_with:link_url'],
            'link_url' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https:\/\/)/'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
        ], ['link_url.regex' => 'Links must start with / (a page on this site) or https://.']));

        return back()->with('status', 'Announcement published.');
    }

    public function toggleAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back()->with('status', $announcement->is_active ? 'Announcement switched on.' : 'Announcement switched off.');
    }

    public function destroyAnnouncement(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('status', 'Announcement removed.');
    }

    /** Validate the tour form, store any new photos and save the tour. */
    protected function saveTour(Request $request, Tour $tour): void
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:6144'];
        $price = ['nullable', 'integer', 'between:1,5000'];
        $bookingUrl = ['nullable', 'url', 'starts_with:https://', 'max:255'];

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'category' => ['required', Rule::in(array_keys(config('site.categories')))],
            'summary' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:6000'],
            'duration' => ['nullable', 'string', 'max:60'],
            'distance' => ['nullable', 'string', 'max:60'],
            'pickup' => ['nullable', 'string', 'max:60'],
            'dropoff' => ['nullable', 'string', 'max:60'],
            'price_group' => $price,
            'price_private' => $price,
            'includes' => ['nullable', 'string', 'max:3000'],
            'bring' => ['nullable', 'string', 'max:3000'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'booking_group_url' => $bookingUrl,
            'booking_private_url' => $bookingUrl,
            'sort_order' => ['nullable', 'integer', 'between:0,9999'],
            'cover_file' => ['nullable', ...$image],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => $image,
            'remove' => ['nullable', 'array'],
            'remove.*' => ['string'],
        ]);

        $existing = $tour->images();
        $remove = array_intersect($data['remove'] ?? [], $existing);
        $gallery = array_values(array_diff($tour->gallery ?? [], $remove));
        $cover = in_array($tour->cover, $remove, true) ? null : $tour->cover;

        foreach ($request->file('photos', []) as $photo) {
            $gallery[] = 'uploads/'.$photo->store('tours', 'uploads');
        }

        if ($file = $request->file('cover_file')) {
            // The old cover stays in the gallery rather than being lost.
            if ($cover) {
                array_unshift($gallery, $cover);
            }
            $cover = 'uploads/'.$file->store('tours', 'uploads');
        }

        $tour->fill([
            ...collect($data)->only([
                'title', 'category', 'summary', 'body', 'duration', 'distance', 'pickup', 'dropoff',
                'price_group', 'price_private', 'booking_group_url', 'booking_private_url',
            ])->all(),
            'slug' => $tour->slug ?: $this->uniqueSlug($data['title']),
            'includes' => Tour::parseLines($data['includes'] ?? null),
            'bring' => Tour::parseLines($data['bring'] ?? null),
            'notes' => Tour::parseLines($data['notes'] ?? null),
            'cover' => $cover ?? array_shift($gallery),
            'gallery' => array_values(array_unique($gallery)),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->boolean('is_published'),
        ])->save();

        Storage::disk('uploads')->delete(array_map(
            fn (string $path) => Str::after($path, 'uploads/'),
            array_filter($remove, fn (string $path) => str_starts_with($path, 'uploads/')),
        ));
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'tour';
        $slug = $base;

        for ($i = 2; Tour::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}
