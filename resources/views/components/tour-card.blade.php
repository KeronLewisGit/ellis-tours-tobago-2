@props(['tour'])
<a href="{{ route('tours.show', $tour) }}" {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-3xl bg-white shadow-card ring-1 ring-line/70 transition hover:-translate-y-1 hover:shadow-lift']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-paper-deep">
        @if ($tour->cover)
            <img src="{{ asset($tour->cover) }}" alt="" class="size-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
        @endif
        <span class="absolute top-4 left-4 rounded-full bg-white/95 px-3 py-1 text-[11px] font-semibold tracking-wide text-ink uppercase shadow-sm">{{ $tour->categoryLabel() }}</span>
        @unless ($tour->is_available)
            <span class="absolute top-4 right-4 rounded-full bg-ink px-3 py-1 text-[11px] font-semibold tracking-wide text-white uppercase">Not running now</span>
        @endunless
    </div>
    <div class="flex flex-1 flex-col p-6">
        <h3 class="font-display text-2xl leading-tight font-semibold transition group-hover:text-brand-700">{{ $tour->title }}</h3>
        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted">{{ $tour->summary }}</p>
        <ul class="mt-4 flex flex-wrap gap-x-4 gap-y-1.5 text-xs font-medium text-ink/75">
            @if ($tour->duration)
                <li class="flex items-center gap-1.5"><x-icon name="clock" class="size-3.5 text-brand-600" /> {{ $tour->duration }}</li>
            @endif
            @if ($tour->distance)
                <li class="flex items-center gap-1.5"><x-icon name="footprints" class="size-3.5 text-brand-600" /> {{ $tour->distance }}</li>
            @endif
            @if ($tour->pickup)
                <li class="flex items-center gap-1.5"><x-icon name="bus" class="size-3.5 text-brand-600" /> Pickup {{ $tour->pickup }}</li>
            @endif
        </ul>
        <div class="mt-5 flex items-end justify-between gap-4 border-t border-line pt-5">
            @if ($tour->fromPrice())
                <p class="text-sm text-muted">
                    {{ count($tour->rates()) > 1 ? 'From' : 'Price' }}
                    <span class="block font-display text-2xl font-semibold text-ink">{{ config('site.currency') }}{{ $tour->fromPrice() }} <span class="font-sans text-xs font-medium text-muted">per person</span></span>
                </p>
            @else
                <p class="text-sm text-muted">Ask for a price</p>
            @endif
            <span class="link-arrow shrink-0">View tour <x-icon name="arrow-right" class="size-4" /></span>
        </div>
    </div>
</a>
