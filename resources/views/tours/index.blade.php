@php
    $site = config('site');
@endphp
<x-layout title="Tours and prices" description="Compare every Ellis Tours Tobago tour: rainforest walks, island tours and boat trips, with what is included and the price per person.">
    <x-page-hero eyebrow="Browse all tours" title="Tours and prices"
        lead="Welcome to Tobago, where the journey is as exhilarating as the destination. Reserve your experience now, with the option to pay online or in cash at pickup.">
        @foreach ($site['categories'] as $slug => $category)
            @if ($tours->has($slug))
                <a href="#{{ $slug }}" class="btn btn-onDark"><x-icon :name="$category['icon']" class="size-4" /> {{ $category['name'] }}</a>
            @endif
        @endforeach
    </x-page-hero>

    @foreach ($site['categories'] as $slug => $category)
        @continue(! $tours->has($slug))
        <section id="{{ $slug }}" @class(['section scroll-mt-24', 'border-y border-line bg-white' => $loop->even])>
            <div class="wrap">
                <div class="grid gap-6 lg:grid-cols-12" data-reveal>
                    <div class="lg:col-span-5">
                        <p class="eyebrow">{{ $category['eyebrow'] }}</p>
                        <h2 class="h-section mt-4">{{ $category['name'] }}</h2>
                    </div>
                    <p class="lead lg:col-span-7">{{ $category['intro'] }}</p>
                </div>

                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tours[$slug] as $tour)
                        <x-tour-card :tour="$tour" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    {{-- Compare --}}
    <section class="section">
        <div class="wrap">
            <div class="max-w-2xl" data-reveal>
                <p class="eyebrow">At a glance</p>
                <h2 class="h-section mt-4">Compare the tours.</h2>
                <p class="lead mt-4">Prices are per person in US dollars. Group tours may include other guests; private tours are for your party only.</p>
            </div>
            <div class="card mt-8 overflow-x-auto !p-0" data-reveal>
                <table class="table-clean min-w-[44rem]">
                    <thead>
                        <tr><th>Tour</th><th>Length</th><th>Pickup</th><th>Back by</th><th class="text-right">Group</th><th class="text-right">Private</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($tours->flatten() as $tour)
                            <tr>
                                <td>
                                    <a href="{{ route('tours.show', $tour) }}" class="font-semibold hover:text-brand-700">{{ $tour->title }}</a>
                                    @unless ($tour->is_available)<span class="ml-1 rounded bg-paper-deep px-1.5 py-0.5 text-[10px] font-semibold text-muted uppercase">Not running now</span>@endunless
                                    <p class="text-xs text-muted">{{ $tour->categoryLabel() }}</p>
                                </td>
                                <td class="whitespace-nowrap">{{ $tour->duration ?? '–' }}</td>
                                <td class="whitespace-nowrap">{{ $tour->pickup ?? '–' }}</td>
                                <td class="whitespace-nowrap">{{ $tour->dropoff ?? '–' }}</td>
                                <td class="text-right font-semibold whitespace-nowrap tabular-nums">{{ $tour->price_group ? $site['currency'].$tour->price_group : '–' }}</td>
                                <td class="text-right font-semibold whitespace-nowrap tabular-nums">{{ $tour->price_private ? $site['currency'].$tour->price_private : '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-sm text-muted">Travelling as a school, a large family or from a cruise ship? <a href="{{ route('book') }}#form-group" class="link">Ask for a group or custom tour</a>.</p>
        </div>
    </section>

    <x-cta-band />
</x-layout>
