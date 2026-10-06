@php
    $site = config('site');
    $guide = $site['guide'];
@endphp
<x-layout title="About Ellis" description="Meet Ellis Clarke, a licensed Tobago tour guide with more than twenty years of experience in the island’s rainforest, bays and reefs.">
    <x-page-hero eyebrow="Your guide" :title="$guide['name']"
        lead="A licensed tour guide who has spent more than twenty years exploring Tobago’s rainforest, coast and reefs, and showing visitors the best of them."
        :image="$guide['image']" :image-alt="$guide['name'].', tour guide, standing on a rainforest trail'">
        <a href="{{ route('tours') }}" class="btn btn-primary">See the tours <x-icon name="arrow-right" class="size-4" /></a>
        <a href="{{ $site['links']['tripadvisor'] }}" target="_blank" rel="noopener" class="btn btn-onDark">Ellis on Tripadvisor <x-icon name="arrow-up-right" class="size-4" /></a>
    </x-page-hero>

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7" data-reveal>
                <p class="eyebrow">In his words</p>
                <blockquote class="mt-4 font-display text-3xl leading-snug font-medium tracking-tight sm:text-4xl">&ldquo;{{ $guide['bio'] }}&rdquo;</blockquote>
                <div class="prose-site mt-8">
                    <p>Ellis Tours Tobago is Ellis and the island he knows best. On the rainforest walks he guides you through the Tobago Main Ridge Forest Reserve, pointing out the birds, wildlife and plants along the trail and explaining how the forest fits together. He has been exploring it for more than twenty years.</p>
                    <p>Away from the forest, the same local knowledge shapes the island tours and boat trips: the lookouts worth stopping at, the fishing villages, the waterfall you can swim under and the place for a proper local lunch.</p>
                    <p>You can join a group tour, or book Ellis privately and set your own pace. Either way, he collects you from where you are staying and brings you back.</p>
                </div>
            </div>
            <div class="lg:col-span-5" data-reveal style="--reveal-delay: 120ms">
                <div class="card bg-white">
                    <h2 class="font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase">What you can count on</h2>
                    <ul class="mt-5 space-y-5">
                        @foreach ($site['promises'] as [$icon, $title, $text])
                            <li class="flex gap-4">
                                <span class="icon-tile"><x-icon :name="$icon" /></span>
                                <span><strong class="block font-semibold">{{ $title }}</strong><span class="text-sm text-muted">{{ $text }}</span></span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Highlights --}}
    <section class="section border-y border-line bg-white">
        <div class="wrap">
            <div class="max-w-2xl" data-reveal>
                <p class="eyebrow">Where Ellis takes you</p>
                <h2 class="h-section mt-4">The places behind the tours.</h2>
            </div>
            <div class="mt-10 grid gap-x-10 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($site['highlights'] as [$icon, $title, $text])
                    <div class="flex gap-4" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 70 }}ms">
                        <span class="icon-tile"><x-icon :name="$icon" /></span>
                        <div>
                            <h3 class="h-card">{{ $title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($reviews->isNotEmpty())
        <section class="section">
            <div class="wrap">
                <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                    <h2 class="h-section">What guests say about Ellis.</h2>
                    <a href="{{ route('reviews') }}" class="link-arrow">All reviews <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div class="mt-8 grid gap-5 lg:grid-cols-2">
                    @foreach ($reviews as $review)
                        <x-review-card :review="$review" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($tours->isNotEmpty())
        <section class="section border-t border-line bg-white">
            <div class="wrap">
                <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                    <h2 class="h-section">Spend a day with Ellis.</h2>
                    <a href="{{ route('tours') }}" class="link-arrow">All tours and prices <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tours as $tour)
                        <x-tour-card :tour="$tour" data-reveal />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-cta-band />
</x-layout>
