@php
    $site = config('site');
    // One flat list for the lightbox, in the same order the photos appear on the page.
    $sections = collect($site['categories'])
        ->map(fn ($category, $slug) => [
            'name' => $category['name'],
            'photos' => $tours->where('category', $slug)
                ->flatMap(fn ($tour) => collect($tour->images())->map(fn ($path) => ['path' => $path, 'tour' => $tour]))
                ->unique('path')
                ->values(),
        ])
        ->filter(fn ($section) => $section['photos']->isNotEmpty());
    $all = $sections->flatMap(fn ($section) => $section['photos']->pluck('path'))->values();
    $position = 0;
@endphp
<x-layout title="Gallery" description="Photos from Ellis Tours Tobago: the Main Ridge rainforest, waterfalls, hummingbirds, Buccoo Reef, the Nylon Pool and Pirate’s Bay.">
    <x-page-hero eyebrow="Gallery" title="Tobago, through our guests’ days."
        lead="Photos from the rainforest, the road and the water. Choose any photo to see it full size.">
        @foreach ($sections as $slug => $section)
            <a href="#gallery-{{ $slug }}" class="btn btn-onDark">{{ $section['name'] }}</a>
        @endforeach
    </x-page-hero>

    <div x-data="gallery(@js($all->map(fn ($path) => asset($path))->all()))" @keydown.escape.window="open = false" @keydown.arrow-right.window="open && move(1)" @keydown.arrow-left.window="open && move(-1)">
        @foreach ($sections as $slug => $section)
            <section id="gallery-{{ $slug }}" @class(['section scroll-mt-24', 'border-y border-line bg-white' => $loop->even])>
                <div class="wrap">
                    <div class="flex flex-wrap items-end justify-between gap-6" data-reveal>
                        <h2 class="h-section">{{ $section['name'] }}</h2>
                        <a href="{{ route('tours') }}#{{ $slug }}" class="link-arrow">See these tours <x-icon name="arrow-right" class="size-4" /></a>
                    </div>
                    <div class="mt-8 columns-2 gap-4 md:columns-3 lg:columns-4">
                        @foreach ($section['photos'] as $photo)
                            <button type="button" class="group relative mb-4 block w-full overflow-hidden rounded-2xl bg-paper-deep ring-1 ring-line" @click="show({{ $position++ }})" aria-label="Open photo: {{ $photo['tour']->title }}">
                                <img src="{{ asset($photo['path']) }}" alt="{{ $photo['tour']->title }}" class="w-full transition duration-700 group-hover:scale-[1.03]" loading="lazy">
                                <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/80 to-transparent p-3 pt-8 text-left text-xs font-semibold text-white opacity-0 transition group-hover:opacity-100">{{ $photo['tour']->title }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach

        {{-- Lightbox --}}
        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-ink/95 p-4" role="dialog" aria-modal="true" aria-label="Photo viewer" @click.self="open = false">
            <img :src="images[index]" alt="" class="max-h-full max-w-full rounded-xl object-contain">
            <button type="button" class="absolute top-4 right-4 grid size-11 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="open = false" aria-label="Close"><x-icon name="x" /></button>
            <button type="button" class="absolute top-1/2 left-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(-1)" aria-label="Previous photo"><x-icon name="chevron-left" /></button>
            <button type="button" class="absolute top-1/2 right-4 grid size-11 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20" @click="move(1)" aria-label="Next photo"><x-icon name="chevron-right" /></button>
            <p class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white" x-text="(index + 1) + ' / ' + images.length"></p>
        </div>
    </div>

    <section class="wrap pb-4 text-center">
        <p class="text-sm text-muted">More photos and videos on <a href="{{ $site['links']['instagram'] }}" target="_blank" rel="noopener" class="link">Instagram</a>, <a href="{{ $site['links']['facebook'] }}" target="_blank" rel="noopener" class="link">Facebook</a> and <a href="{{ $site['links']['tiktok'] }}" target="_blank" rel="noopener" class="link">TikTok</a>.</p>
    </section>

    <x-cta-band />
</x-layout>
