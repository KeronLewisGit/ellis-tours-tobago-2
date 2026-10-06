<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
{
    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(isset(Enquiry::TYPES[$type]), 404);

        $back = url()->previous(route('contact')).'#form-'.$type;

        // Honeypot: bots fill the hidden "website" field. Pretend it worked.
        if ($request->filled('website')) {
            return redirect()->to($back)->with('enquiry_success', ['type' => $type, 'reference' => null]);
        }

        $validator = Validator::make($request->all(), $this->rules($type), [
            'tour.required' => 'Choose a tour.',
            'tour.in' => 'Choose one of the tours listed.',
            'tour_date.required' => 'Choose the date you would like to go.',
            'tour_date.after' => 'Choose a date in the future.',
            'interests.required' => 'Choose at least one thing you would like to see.',
        ], [
            'adults' => 'number of adults',
            'children' => 'number of children',
            'pickup' => 'pickup location',
            'payment' => 'payment option',
            'heard' => 'how you heard about us',
            'group_type' => 'type of group',
            'group_size' => 'group size',
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator, $type)->withInput();
        }

        $data = $validator->validated();

        if (array_key_exists('date_flexible', $data)) {
            $data['date_flexible'] = $request->boolean('date_flexible') ? 'Yes' : null;
        }

        if ($type === 'booking') {
            $data = $this->describeBooking($data);
        }

        $enquiry = Enquiry::create([
            'type' => $type,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'summary' => $this->summary($type, $data),
            'data' => Arr::except($data, ['name', 'email', 'phone']),
        ]);

        return redirect()->to($back)->with('enquiry_success', [
            'type' => $type,
            'reference' => $enquiry->reference,
            'email' => $enquiry->email,
        ]);
    }

    protected function rules(string $type): array
    {
        $person = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
        ];
        $phone = ['required', 'string', 'min:7', 'max:40'];

        return match ($type) {
            'booking' => $person + [
                'phone' => $phone,
                'tour' => ['required', Rule::in(Tour::bookable()->pluck('slug')->all())],
                'rate' => ['nullable', Rule::in(array_keys(Tour::RATES))],
                'tour_date' => ['required', 'date', 'after:today'],
                'adults' => ['required', 'integer', 'between:1,40'],
                'children' => ['nullable', 'integer', 'between:0,40'],
                'pickup' => ['required', 'string', 'max:160'],
                'payment' => ['required', Rule::in(config('site.payment_methods'))],
                'contact_method' => ['required', Rule::in(config('site.contact_methods'))],
                'heard' => ['nullable', Rule::in(config('site.heard'))],
                'message' => ['nullable', 'string', 'max:2000'],
            ],
            'group' => $person + [
                'phone' => $phone,
                'group_type' => ['required', Rule::in(config('site.group_types'))],
                'group_size' => ['required', 'integer', 'between:2,300'],
                'tour_date' => ['nullable', 'date', 'after:today'],
                'date_flexible' => ['nullable', 'boolean'],
                'interests' => ['required', 'array', 'min:1'],
                'interests.*' => [Rule::in(config('site.interests'))],
                'message' => ['nullable', 'string', 'max:4000'],
            ],
            'contact' => $person + [
                'phone' => ['nullable', 'string', 'max:40'],
                'topic' => ['nullable', Rule::in(config('site.topics'))],
                'message' => ['required', 'string', 'min:5', 'max:4000'],
            ],
        };
    }

    /**
     * Swap the tour slug and rate key for the wording staff will read in the inbox.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function describeBooking(array $data): array
    {
        $tour = Tour::where('slug', $data['tour'])->firstOrFail();
        $rates = collect($tour->rates())->keyBy('key');
        $rate = $rates->get($data['rate'] ?? '') ?? $rates->first();

        $data['tour'] = $tour->title;
        $data['rate'] = $rate ? sprintf('%s (%s%d per person)', $rate['label'], config('site.currency'), $rate['price']) : null;
        $data['children'] = ((int) ($data['children'] ?? 0)) ?: null;

        return $data;
    }

    protected function summary(string $type, array $data): string
    {
        return match ($type) {
            'booking' => sprintf(
                '%s · %s · %d %s',
                $data['tour'],
                Carbon::parse($data['tour_date'])->format('j M Y'),
                $guests = (int) $data['adults'] + (int) ($data['children'] ?? 0),
                $guests === 1 ? 'guest' : 'guests',
            ),
            'group' => $data['group_type'].' · '.$data['group_size'].' people',
            'contact' => $data['topic'] ?? 'General enquiry',
        };
    }
}
