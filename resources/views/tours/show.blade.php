@php
    $site = config('site');
    $images = $tour->images();
    $rates = $tour->rates();
    $whatsapp = \App\Support\Content::whatsapp("Hi Ellis, I would like to ask about the {$tour->title}.");
    $facts = array_filter([
        ['clock', 'Length', $tour->duration],
        ['footprints', 'Walk', $tour->distance],
        ['bus', 'Hotel pickup', $tour->pickup],
        ['sunrise', 'Back by', $tour->dropoff],
    ], fn (array $fact) => filled($fact[2]));
@endphp
<x-layout :title="$tour->title" :description="$tour->summary">
    <article x-data="gallery(@js(array_map(fn ($path) => asset($path), $images)))" @keydown.escape.window="open = false" @keydown.arrow-right.window="open && move(1)" @keydown.arrow-left.window="open && move(-1)">
        <header class="relative overflow-hidden bg-ink text-white">
            @if ($tour->cover)
                <img src="{{ asset($tour->cover) }}" alt="" class="absolute inset-0 size-full object-cover opacity-35" fetchpriority="high">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/80 to-ink/50"></div>
            <div class="wrap relative py-14 sm:py-20">
                <nav class="mb-6 flex items-center gap-2 text-xs font-medium text-white/60" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white">Home</a><span>/</span>
                    <a href="{{ route('tours') }}" class="hover:text-white">Tours</a><span>/</span>
                    <a href="{{ route('tours') }}#{{ $tour->category }}" class="hover:text-white">{{ $tour->categoryLabel() }}</a>
                </nav>
                <p class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    <span class="rounded-full bg-brand-600 px-2.5 py-1 text-white">{{ $tour->categoryLabel() }}</span>
                    @unless ($tour->is_available)<span class="rounded-full bg-white/15 px-2.5 py-1 text-white">Not running at the moment</span>@endunless
                    @unless ($tour->is_published)<span class="rounded-full bg-white/15 px-2.5 py-1 text-white">Draft, visible to staff only</span>@endunless
                </p>
                <h1 class="h-display mt-5 max-w-4xl">{{ $tour->title }}</h1>
                <p class="mt-5 max-w-2xl text-xl leading-relaxed text-white/80">{{ $tour->summary }}</p>

                @if ($facts)
                    <dl class="mt-8 flex flex-wrap gap-x-10 gap-y-4 text-sm">
                        @foreach ($facts as [$icon, $label, $value])
                            <div class="flex items-center gap-3">
                                <span class="grid size-10 place-items-center rounded-xl bg-white/10 text-brand-300"><x-icon :name="$icon" class="size-[18px]" /></span>
                                <div><dt class="text-xs text-white/55">{{ $label }}</dt><dd class="font-semibold">{{ $value }}</dd></div>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </header>

        <div class="wrap py-12 sm:py-16">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    @if ($tour->body)
                        <div class="prose-site">
                            @foreach (preg_split('/\R{2,}/', trim($tour->body)) as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if ($tour->includes)
                        <section class="mt-10">
                            <h2 class="font-display text-2xl font-semibold">What is included</h2>
                            <ul class="mt-5 grid gap-3">
                                @foreach ($tour->includes as $item)
                                    <li class="flex gap-3 text-[15px]"><x-icon name="circle-check" class="mt-0.5 size-5 shrink-0 text-emerald-600" /> <span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($tour->bring)
                        <section class="mt-10">
                            <h2 class="font-display text-2xl font-semibold">What to wear and carry</h2>
                            <ul class="mt-5 grid gap-x-6 gap-y-3 sm:grid-cols-2">
                                @foreach ($tour->bring as $item)
                                    <li class="flex gap-3 text-[15px]"><x-icon name="backpack" class="mt-0.5 size-5 shrink-0 text-brand-600" /> <span>{{ $item }}</span></li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($tour->notes)
                        <section class="mt-10 rounded-3xl bg-paper-deep/70 p-6 ring-1 ring-line">
                            <h2 class="flex items-center gap-2 font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase"><x-icon name="info" class="size-4" /> Good to know</h2>
                            <ul class="mt-3 space-y-2 text-sm text-ink/80">
                                @foreach ($tour->notes as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>

                {{-- Booking card --}}
                <aside class="lg:col-span-5">
                    <div class="card lg:sticky lg:top-28" id="book">
                        @if ($tour->is_available && $rates)
                            <h2 class="font-display text-2xl font-semibold">Book this tour</h2>
                            <p class="mt-1 text-sm text-muted">Prices are per person, in US dollars.</p>

                            <ul class="mt-5 space-y-3">
                                @foreach ($rates as $rate)
                                    <li class="rounded-2xl bg-paper p-4 ring-1 ring-line">
                                        <div class="flex items-baseline justify-between gap-4">
                                            <p class="font-semibold">{{ $rate['label'] }}</p>
                                            <p class="font-display text-3xl font-semibold">{{ $site['currency'] }}{{ $rate['price'] }}</p>
                                        </div>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <a href="{{ route('book', ['tour' => $tour->slug, 'rate' => $rate['key']]) }}" class="btn btn-primary flex-1 !py-2.5">Request to book</a>
                                            @if ($rate['url'])
                                                <a href="{{ $rate['url'] }}" target="_blank" rel="noopener" class="btn btn-light flex-1 !py-2.5">Pay online <x-icon name="arrow-up-right" class="size-4" /></a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-6 border-t border-line pt-6">
                                <x-date-check :booked="$booked" :tour="$tour->slug" />
                            </div>
                        @else
                            <h2 class="font-display text-2xl font-semibold">Not running at the moment</h2>
                            <p class="mt-2 text-sm text-muted">This tour is not taking bookings right now. Send Ellis a message to find out when it is back, or choose another tour.</p>
                            <a href="{{ route('tours') }}" class="btn btn-primary mt-5 w-full">See the tours that are running</a>
                        @endif

                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-3 w-full"><x-icon name="message-circle" class="size-4" /> Ask Ellis on WhatsApp</a>
                    </div>
                </aside>
            </div>

            @if ($images)
                <section class="mt-16">
                    <h2 class="font-display text-2xl font-semibold">Photos from this tour</h2>
                    <div class="mt-6 columns-2 gap-4 lg:columns-4">
                        @foreach ($images as $path)
                            <button type="button" class="group mb-4 block w-full overflow-hidden rounded-2xl bg-paper-deep ring-1 ring-line" @click="show({{ $loop->index }})" aria-label="Open photo {{ $loop->iteration }} of {{ count($images) }}">
                                <img src="{{ asset($path) }}" alt="{{ $tour->title }}, photo {{ $loop->iteration }}" class="w-full transition duration-700 group-hover:scale-[1.03]" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Lightbox --}}
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink/95 p-4" role="dialog" aria-modal="true" aria-label="Photo viewer" @click.self="open = false">
            <img :src="images[index]" alt="" class="max-h-full max-w-full rounded-xl object-contain">
            <button type="button" class="absolute top-4 right-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="open = false" aria-label="Close"><x-icon name="x" /></button>
            <template x-if="images.length > 1">
                <div>
                    <button type="button" class="absolute top-1/2 left-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(-1)" aria-label="Previous photo"><x-icon name="chevron-left" /></button>
                    <button type="button" class="absolute top-1/2 right-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(1)" aria-label="Next photo"><x-icon name="chevron-right" /></button>
                    <p class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white" x-text="(index + 1) + ' / ' + images.length"></p>
                </div>
            </template>
        </div>
    </article>

    @if ($reviews->isNotEmpty())
        <section class="section border-t border-line bg-white">
            <div class="wrap">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="h-section">What guests say about this tour</h2>
                    <a href="{{ route('reviews') }}" class="link-arrow">All reviews <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div class="mt-8 grid gap-5 lg:grid-cols-2">
                    @foreach ($reviews as $review)
                        <x-review-card :review="$review" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($more->isNotEmpty())
        <section class="section border-t border-line">
            <div class="wrap">
                <h2 class="h-section">More tours</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($more as $other)
                        <x-tour-card :tour="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
