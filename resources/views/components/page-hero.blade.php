@props(['eyebrow' => null, 'title', 'lead' => null, 'image' => null, 'imageAlt' => '', 'crumb' => null])
<section class="relative overflow-hidden bg-ink text-white">
    <div class="grid-ink absolute inset-0 opacity-60"></div>
    <div class="absolute -top-40 -right-32 size-[30rem] rounded-full bg-sea-600/25 blur-3xl"></div>
    <div class="wrap relative grid items-center gap-10 py-14 sm:py-20 lg:grid-cols-12">
        <div class="{{ $image ? 'lg:col-span-7' : 'lg:col-span-9' }}">
            <nav class="mb-6 flex items-center gap-2 text-xs font-medium text-white/50" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-white">Home</a>
                @if ($crumb)
                    <span>/</span>
                    <a href="{{ $crumb[1] }}" class="transition hover:text-white">{{ $crumb[0] }}</a>
                @endif
                <span>/</span>
                <span class="text-white/80">{{ $title }}</span>
            </nav>
            @if ($eyebrow)
                <p class="eyebrow !text-brand-300">{{ $eyebrow }}</p>
            @endif
            <h1 class="h-display mt-4">{{ $title }}</h1>
            @if ($lead)
                <p class="mt-5 max-w-2xl text-lg leading-relaxed text-white/70">{{ $lead }}</p>
            @endif
            @if ($slot->isNotEmpty())
                <div class="mt-8 flex flex-wrap items-center gap-3">{{ $slot }}</div>
            @endif
        </div>
        @if ($image)
            <div class="relative lg:col-span-5">
                <div class="relative mx-auto aspect-[4/5] max-w-sm overflow-hidden rounded-[2rem] ring-1 ring-white/15 lg:ml-auto">
                    <img src="{{ asset($image) }}" alt="{{ $imageAlt }}" class="size-full object-cover">
                </div>
            </div>
        @endif
    </div>
</section>
