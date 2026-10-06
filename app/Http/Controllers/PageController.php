<?php

namespace App\Http\Controllers;

use App\Models\BookedDate;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /** Static pages searchable from the header: [route, title, description, keywords]. */
    protected const PAGES = [
        ['tours', 'Tours', 'Rainforest walks, island tours and boat tours in Tobago, with prices.', 'tours prices excursions trips things to do tobago'],
        ['book', 'Book a tour', 'Check your date and send a booking request. Pay online or in cash at pickup.', 'book booking reserve date availability pay payment cash online group school cruise'],
        ['about', 'About Ellis', 'Ellis Clarke, a licensed Tobago tour guide with more than twenty years of experience.', 'about ellis clarke guide licensed certified experience'],
        ['gallery', 'Gallery', 'Photos from the rainforest, the reef and the coast.', 'gallery photos pictures images waterfall hummingbird reef'],
        ['reviews', 'Reviews', 'What guests say about their day with Ellis Tours.', 'reviews testimonials tripadvisor feedback rating'],
        ['faq', 'Questions and answers', 'Payment, pickup, what to wear, lunch, rain and cruise ship visits.', 'faq questions pay pickup wear bring lunch rain weather cruise kids fitness'],
        ['contact', 'Contact', 'WhatsApp, call or email Ellis.', 'contact phone whatsapp email location pigeon point crown point'],
    ];

    public function home(): View
    {
        return view('pages.home', [
            'featured' => Tour::bookable()->where('is_featured', true)->take(6)->get(),
            'booked' => BookedDate::publicMap(),
            'reviews' => Review::published()->take(3)->get(),
            'tourCount' => Tour::bookable()->count(),
            'fromPrice' => Tour::bookable()->get()->map->fromPrice()->filter()->min(),
        ]);
    }

    public function book(Request $request): View
    {
        $tours = Tour::bookable()->get();

        return view('pages.book', [
            'tours' => $tours,
            'selected' => $tours->firstWhere('slug', $request->query('tour')),
            'booked' => BookedDate::publicMap(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'tours' => Tour::bookable()->where('is_featured', true)->take(3)->get(),
            'reviews' => Review::published()->take(2)->get(),
        ]);
    }

    public function gallery(): View
    {
        return view('pages.gallery', ['tours' => Tour::published()->get()]);
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function search(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $terms = collect(preg_split('/\s+/', Str::lower($query)))->filter(fn ($t) => strlen($t) > 1);
        $results = collect();

        if ($terms->isNotEmpty()) {
            $matches = fn (string $haystack) => $terms->every(fn ($t) => str_contains(Str::lower($haystack), $t));

            foreach (Tour::published()->get() as $tour) {
                if ($matches($tour->title.' '.$tour->summary.' '.$tour->body.' '.$tour->categoryLabel().' '.implode(' ', $tour->includes ?? []))) {
                    $results->push(['type' => 'Tour', 'title' => $tour->title, 'text' => $tour->summary, 'url' => route('tours.show', $tour)]);
                }
            }

            foreach (self::PAGES as [$route, $title, $description, $keywords]) {
                if ($matches("$title $description $keywords")) {
                    $results->push(['type' => 'Page', 'title' => $title, 'text' => $description, 'url' => route($route)]);
                }
            }

            foreach (config('site.faqs') as [$question, $answer]) {
                if ($matches("$question $answer")) {
                    $results->push(['type' => 'Question', 'title' => $question, 'text' => Str::limit($answer, 140), 'url' => route('faq').'#'.Str::slug($question)]);
                }
            }
        }

        return view('pages.search', ['query' => $query, 'results' => $results]);
    }
}
