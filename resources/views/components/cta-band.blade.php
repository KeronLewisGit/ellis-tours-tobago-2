@props([
    'title' => 'Ready to see Tobago?',
    'text' => 'Pick a tour, check your date and book. Pay online, or in cash when Ellis picks you up. Prefer to talk it through first? Send a WhatsApp message.',
])
<section class="wrap py-16 sm:py-20">
    <div class="relative overflow-hidden rounded-[2rem] bg-ink px-6 py-14 text-center text-white sm:px-16 sm:py-20" data-reveal>
        <img src="{{ asset('images/photos/img_3304.webp') }}" alt="" class="absolute inset-0 size-full object-cover opacity-30" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/70 to-ink/40"></div>
        <div class="relative mx-auto max-w-2xl">
            <p class="eyebrow !text-brand-300">{{ config('site.tagline') }}</p>
            <h2 class="mt-4 font-display text-4xl leading-[1.05] font-semibold tracking-tight sm:text-5xl">{{ $title }}</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-white/75">{{ $text }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if ($slot->isNotEmpty())
                    {{ $slot }}
                @else
                    <a href="{{ route('book') }}" class="btn btn-primary !px-6 !py-3.5 !text-base">Book a tour <x-icon name="arrow-right" class="size-4" /></a>
                    <a href="{{ \App\Support\Content::whatsapp('Hi Ellis, I would like to ask about a tour in Tobago.') }}" target="_blank" rel="noopener" class="btn btn-onDark !px-6 !py-3.5 !text-base"><x-icon name="message-circle" class="size-4" /> WhatsApp Ellis</a>
                @endif
            </div>
        </div>
    </div>
</section>
