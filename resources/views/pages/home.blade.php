@php
    $site = config('site');
@endphp
<x-layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink text-white">
        <div class="grid-ink absolute inset-0 opacity-50"></div>
        <div class="absolute -top-48 -left-40 size-[38rem] rounded-full bg-sea-600/20 blur-3xl"></div>

        <div class="wrap relative grid items-center gap-12 pt-12 pb-16 sm:pt-16 sm:pb-24 lg:grid-cols-12">
            <div class="lg:col-span-6">
                <p class="eyebrow !text-brand-300">Rainforest walks &middot; Island tours &middot; Boat tours</p>
                <h1 class="mt-6 font-display text-5xl leading-[1.02] font-semibold tracking-tight sm:text-6xl lg:text-7xl">
                    Explore the greatest <span class="text-brand-300">little island.</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-white/70">
                    {{ $site['intro'] }}
                </p>

                <div class="mt-8 max-w-xl rounded-3xl bg-white/5 p-5 ring-1 ring-white/10 sm:p-6">
                    <x-date-check :booked="$booked" dark />
                </div>

                <ul class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-sm text-white/70">
                    <li class="flex items-center gap-2"><x-icon name="badge-check" class="size-[18px] text-brand-300" /> Licensed guide, {{ $site['years'] }}+ years</li>
                    <li class="flex items-center gap-2"><x-icon name="bus" class="size-[18px] text-brand-300" /> Hotel pickup included</li>
                    <li class="flex items-center gap-2"><x-icon name="wallet" class="size-[18px] text-brand-300" /> Pay online or in cash</li>
                </ul>
            </div>

            {{-- Photo collage --}}
            <div class="relative lg:col-span-6" data-reveal style="--reveal-delay: 150ms">
                <div class="grid grid-cols-5 items-end gap-3 sm:gap-4">
                    <div class="col-span-3 aspect-[3/4] overflow-hidden rounded-[2rem] ring-1 ring-white/15">
                        <img src="{{ asset('images/photos/img_4841.webp') }}" alt="A waterfall dropping into a pool in the Main Ridge rainforest" class="size-full object-cover" fetchpriority="high">
                    </div>
                    <div class="col-span-2 space-y-3 sm:space-y-4">
                        <div class="aspect-square overflow-hidden rounded-3xl ring-1 ring-white/15">
                            <img src="{{ asset('images/photos/img_4649.webp') }}" alt="Ellis pointing out a bay from a lookout above the coast" class="size-full object-cover">
                        </div>
                        <div class="aspect-[4/5] overflow-hidden rounded-3xl ring-1 ring-white/15">
                            <img src="{{ asset('images/photos/img_2445.webp') }}" alt="Snorkellers in clear turquoise water over the reef" class="size-full object-cover">
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-5 left-4 hidden rounded-2xl bg-white px-5 py-3 text-ink shadow-lift sm:block animate-float">
                    <p class="text-[10px] font-semibold tracking-widest text-muted uppercase">Main Ridge Forest Reserve</p>
                    <p class="font-display text-lg font-semibold">Five waterfalls on one walk</p>
                </div>
            </div>
        </div>
        <div class="gilt h-px w-full"></div>
    </section>

    {{-- Stats --}}
    <section class="border-b border-line bg-white">
        <dl class="wrap grid grid-cols-2 gap-y-8 py-10 text-center lg:grid-cols-4">
            @foreach ([
                ['', $site['years'], '+', 'years guiding in Tobago'],
                ['', $tourCount, '', 'tours to choose from'],
                ['', 5, '', 'waterfalls on the long rainforest walk'],
                [$site['currency'], $fromPrice, '', 'per person, where prices start'],
            ] as [$prefix, $number, $suffix, $label])
                <div class="px-2">
                    <dd class="font-display text-4xl font-semibold text-brand-700 sm:text-5xl">{{ $prefix }}<span x-data="countUp({{ (int) $number }})" x-text="value">{{ $number }}</span>{{ $suffix }}</dd>
                    <dt class="mt-1 text-sm text-muted">{{ $label }}</dt>
                </div>
            @endforeach
        </dl>
    </section>

    {{-- Tour types --}}
    <section class="section">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div class="max-w-2xl">
                    <p class="eyebrow">Three ways to see Tobago</p>
                    <h2 class="h-section mt-4">On foot, by road or on the water.</h2>
                </div>
                <a href="{{ route('tours') }}" class="link-arrow">All tours and prices <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($site['categories'] as $slug => $category)
                    <a href="{{ route('tours') }}#{{ $slug }}" data-reveal style="--reveal-delay: {{ $loop->index * 70 }}ms"
                        class="group relative flex min-h-96 flex-col justify-end overflow-hidden rounded-3xl bg-ink p-6 text-white shadow-card transition hover:-translate-y-1 hover:shadow-lift">
                        <img src="{{ asset($category['image']) }}" alt="" class="absolute inset-0 size-full object-cover opacity-80 transition duration-700 group-hover:scale-105" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/55 to-transparent"></div>
                        <div class="relative">
                            <span class="grid size-11 place-items-center rounded-2xl bg-white/15 backdrop-blur"><x-icon :name="$category['icon']" /></span>
                            <p class="mt-4 text-[11px] font-semibold tracking-[0.16em] text-brand-200 uppercase">{{ $category['eyebrow'] }}</p>
                            <h3 class="mt-1 font-display text-3xl font-semibold">{{ $category['name'] }}</h3>
                            <p class="mt-1.5 text-sm text-white/80">{{ $category['summary'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Popular tours --}}
    <section class="section border-y border-line bg-white">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div class="max-w-2xl">
                    <p class="eyebrow">Popular tours</p>
                    <h2 class="h-section mt-4">Pick your day out.</h2>
                    <p class="lead mt-4">Every tour includes hotel pickup and drop-off. Prices are per person, in US dollars.</p>
                </div>
                <a href="{{ route('tours') }}" class="btn btn-dark">Compare all tours <x-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $tour)
                    <x-tour-card :tour="$tour" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Highlights --}}
    <section class="section">
        <div class="wrap">
            <div class="max-w-2xl" data-reveal>
                <p class="eyebrow">What you will see</p>
                <h2 class="h-section mt-4">Rainforest, reef and everything between.</h2>
            </div>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($site['highlights'] as [$icon, $title, $text, $image])
                    <div class="group overflow-hidden rounded-3xl bg-white shadow-card ring-1 ring-line/70" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 70 }}ms">
                        <div class="aspect-[16/10] overflow-hidden bg-paper-deep">
                            <img src="{{ asset($image) }}" alt="" class="size-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                        </div>
                        <div class="flex gap-4 p-6">
                            <span class="icon-tile"><x-icon :name="$icon" /></span>
                            <div>
                                <h3 class="h-card">{{ $title }}</h3>
                                <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $text }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Meet Ellis --}}
    <section class="relative overflow-hidden bg-ink py-16 text-white sm:py-24">
        <div class="grid-ink absolute inset-0 opacity-50"></div>
        <div class="wrap relative grid items-center gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4" data-reveal>
                <div class="mx-auto aspect-[4/5] max-w-xs overflow-hidden rounded-[2rem] ring-1 ring-white/15">
                    <img src="{{ asset($site['guide']['image']) }}" alt="{{ $site['guide']['name'] }}, tour guide, standing on a rainforest trail" class="size-full object-cover" loading="lazy">
                </div>
            </div>
            <div class="lg:col-span-8" data-reveal style="--reveal-delay: 120ms">
                <p class="eyebrow !text-brand-300">Your guide</p>
                <h2 class="h-section mt-4">{{ $site['guide']['name'] }}</h2>
                <p class="mt-5 max-w-2xl font-display text-2xl leading-snug font-medium text-white/90">&ldquo;{{ $site['guide']['bio'] }}&rdquo;</p>
                <ul class="mt-8 grid gap-5 sm:grid-cols-2">
                    @foreach ($site['promises'] as [$icon, $title, $text])
                        <li class="flex gap-4">
                            <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-white/10 text-brand-300"><x-icon :name="$icon" /></span>
                            <span><strong class="block font-semibold">{{ $title }}</strong><span class="text-sm text-white/65">{{ $text }}</span></span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('about') }}" class="btn btn-onDark mt-8">More about Ellis <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4" data-reveal>
                <p class="eyebrow">How booking works</p>
                <h2 class="h-section mt-4">From this page to the trailhead.</h2>
                <p class="lead mt-4">Reserve in a couple of minutes. You do not need to pay anything up front unless you want to.</p>
                <a href="{{ route('faq') }}" class="link-arrow mt-6">Questions and answers <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <ol class="grid gap-5 sm:grid-cols-2 lg:col-span-8">
                @foreach ($site['process'] as [$icon, $title, $text])
                    <li class="card" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                        <div class="flex items-center justify-between">
                            <span class="icon-tile"><x-icon :name="$icon" /></span>
                            <span class="font-display text-4xl font-semibold text-brand-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="h-card mt-5">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Reviews --}}
    @if ($reviews->isNotEmpty())
        <section class="section border-y border-line bg-white">
            <div class="wrap">
                <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                    <div class="max-w-2xl">
                        <p class="eyebrow">In their words</p>
                        <h2 class="h-section mt-4">What guests say.</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('reviews') }}" class="btn btn-dark">All reviews</a>
                        <a href="{{ $site['links']['tripadvisor'] }}" target="_blank" rel="noopener" class="btn btn-light">Tripadvisor <x-icon name="arrow-up-right" class="size-4" /></a>
                    </div>
                </div>
                <div class="mt-10 grid gap-5 lg:grid-cols-3">
                    @foreach ($reviews as $review)
                        <x-review-card :review="$review" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Instagram --}}
    <section class="section">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                <div>
                    <p class="eyebrow">{{ $site['social']['instagram_handle'] }}</p>
                    <h2 class="h-section mt-4">Happy days, as they happen.</h2>
                </div>
                <a href="{{ $site['links']['instagram'] }}" target="_blank" rel="noopener" class="btn btn-light">Follow on Instagram <x-icon name="arrow-up-right" class="size-4" /></a>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                @foreach ($site['feed'] as [$image, $code, $alt])
                    <a href="https://www.instagram.com/p/{{ $code }}/" target="_blank" rel="noopener" class="group relative block aspect-square overflow-hidden rounded-2xl bg-paper-deep" data-reveal style="--reveal-delay: {{ ($loop->index % 4) * 60 }}ms" aria-label="{{ $alt }}. View this post on Instagram">
                        <img src="{{ asset('images/feed/'.$image) }}" alt="" class="size-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                        <span class="absolute inset-0 grid place-items-center bg-ink/0 text-white opacity-0 transition group-hover:bg-ink/40 group-hover:opacity-100"><x-icon name="arrow-up-right" class="size-6" /></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layout>
