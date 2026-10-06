<?php

namespace App\Http\Controllers;

use App\Models\BookedDate;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(): View
    {
        return view('tours.index', ['tours' => Tour::published()->get()->groupBy('category')]);
    }

    public function show(Tour $tour): View
    {
        abort_unless($tour->is_published || auth()->check(), 404);

        return view('tours.show', [
            'tour' => $tour,
            'more' => Tour::bookable()->whereKeyNot($tour->id)->take(3)->get(),
            'booked' => BookedDate::publicMap(),
            'reviews' => Review::published()->where('tour_title', $tour->title)->take(2)->get(),
        ]);
    }
}
