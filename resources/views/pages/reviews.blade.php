@php
    $site = config('site');
    $bag = 'review';
    $errorBag = $errors->getBag($bag);
    $oldRating = $errorBag->any() ? (int) old('rating') : 0;
@endphp
<x-layout title="Reviews" description="What guests say about their day with Ellis Tours Tobago, and how to leave your own review.">
    <x-page-hero eyebrow="Reviews" title="What guests say."
        lead="Words from people who have walked the rainforest and sailed the reef with Ellis. Been on a tour? We would love to hear about your day.">
        <a href="#form-review" class="btn btn-primary">Leave a review <x-icon name="pencil" class="size-4" /></a>
        <a href="{{ $site['links']['tripadvisor'] }}" target="_blank" rel="noopener" class="btn btn-onDark">Read more on Tripadvisor <x-icon name="arrow-up-right" class="size-4" /></a>
    </x-page-hero>

    <section class="section">
        <div class="wrap">
            @if ($reviews->isEmpty())
                <p class="card text-center text-muted">No reviews have been published yet. Be the first to tell us about your day.</p>
            @else
                <div class="columns-1 gap-5 md:columns-2 lg:columns-3">
                    @foreach ($reviews as $review)
                        <x-review-card :review="$review" class="mb-5 break-inside-avoid" data-reveal />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section border-t border-line bg-white">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">Your turn</p>
                <h2 class="h-section mt-4">Tell us about your day.</h2>
                <p class="lead mt-4">Your review helps other visitors choose their tour, and it means a great deal to Ellis. We read every review before it appears here.</p>
                <a href="{{ $site['links']['tripadvisor'] }}" target="_blank" rel="noopener" class="link-arrow mt-6">Or review us on Tripadvisor <x-icon name="arrow-up-right" class="size-4" /></a>
            </div>
            <div class="lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
                <form id="form-review" method="POST" action="{{ route('reviews.store') }}" class="card scroll-mt-28 bg-paper">
                    @csrf
                    @if (session('review_success'))
                        <div class="mb-6 flex gap-4 rounded-2xl bg-emerald-50 p-5 text-emerald-950 ring-1 ring-emerald-200" role="status">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-emerald-600 text-white"><x-icon name="check" class="size-5" /></span>
                            <div>
                                <p class="font-display text-xl font-semibold">Thank you for your review.</p>
                                <p class="mt-0.5 text-sm text-emerald-900/80">It will appear on this page once Ellis has read it.</p>
                            </div>
                        </div>
                    @elseif ($errorBag->any())
                        <div class="mb-6 flex gap-3 rounded-2xl bg-rose-50 p-4 text-sm text-rose-900 ring-1 ring-rose-200" role="alert">
                            <x-icon name="triangle-alert" class="mt-0.5 size-5 shrink-0 text-rose-600" />
                            <p><strong class="font-semibold">Please check the form.</strong> {{ $errorBag->count() }} {{ Str::plural('field', $errorBag->count()) }} need your attention.</p>
                        </div>
                    @endif

                    <fieldset x-data="{ rating: {{ $oldRating }}, hover: 0 }">
                        <legend class="label">Your rating <span class="text-brand-600" aria-hidden="true">*</span></legend>
                        <div class="flex items-center gap-1" @mouseleave="hover = 0">
                            @for ($star = 1; $star <= 5; $star++)
                                <label class="cursor-pointer rounded-lg p-1 has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-brand-600" @mouseenter="hover = {{ $star }}">
                                    <input type="radio" name="rating" value="{{ $star }}" class="sr-only" x-model.number="rating" required @checked($oldRating === $star)>
                                    <span class="sr-only">{{ $star }} {{ Str::plural('star', $star) }}</span>
                                    <x-icon name="star" class="size-8 transition" ::class="(hover || rating) >= {{ $star }} ? 'fill-brand-400 text-brand-400' : 'text-line'" />
                                </label>
                            @endfor
                        </div>
                        @if ($errorBag->has('rating'))
                            <p class="field-error">{{ $errorBag->first('rating') }}</p>
                        @endif
                    </fieldset>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <x-field name="name" label="Your name" :bag="$bag" required autocomplete="name" hint="Shown with your review. A first name is fine." />
                        <x-field name="origin" label="Where are you from?" :bag="$bag" placeholder="Optional" autocomplete="country-name" />
                        <x-field name="tour_title" type="select" label="Which tour did you take?" :bag="$bag" :options="$tours" placeholder="Optional" class="sm:col-span-2" />
                        <x-field name="body" type="textarea" label="Your review" :bag="$bag" required rows="5" minlength="20" maxlength="1500" class="sm:col-span-2" placeholder="What did you see? What would you tell a friend?" />
                    </div>

                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-5">Send review <x-icon name="send" class="size-4" /></button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
