<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('pages.reviews', [
            'reviews' => Review::published()->get(),
            'tours' => Tour::published()->pluck('title')->all(),
        ]);
    }

    /** Reviews from the website wait for approval in the admin before they are shown. */
    public function store(Request $request): RedirectResponse
    {
        $back = route('reviews').'#form-review';

        // Honeypot: bots fill the hidden "website" field. Pretend it worked.
        if ($request->filled('website')) {
            return redirect()->to($back)->with('review_success', true);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:80'],
            'origin' => ['nullable', 'string', 'max:80'],
            'tour_title' => ['nullable', Rule::in(Tour::published()->pluck('title')->all())],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'min:20', 'max:1500'],
        ], [
            'rating.required' => 'Choose a star rating.',
            'body.min' => 'Tell us a little more about your day (at least 20 characters).',
        ], [
            'origin' => 'where you are from',
            'tour_title' => 'tour',
            'body' => 'review',
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator, 'review')->withInput();
        }

        Review::create([...$validator->validated(), 'source' => 'website', 'is_published' => false]);

        return redirect()->to($back)->with('review_success', true);
    }
}
