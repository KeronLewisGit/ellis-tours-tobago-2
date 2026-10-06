@props(['title' => null, 'description' => null])
@php
    $site = config('site');
    $categories = $site['categories'];
    $current = Route::currentRouteName();
    $announcement = \App\Models\Announcement::current();
    $links = [
        ['gallery', 'Gallery', ['gallery']],
        ['reviews', 'Reviews', ['reviews']],
        ['about', 'About Ellis', ['about']],
        ['faq', 'FAQ', ['faq']],
        ['contact', 'Contact', ['contact']],
    ];
    $whatsapp = \App\Support\Content::whatsapp('Hi Ellis, I would like to ask about a tour in Tobago.');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ $site['name'] }} · {{ $site['descriptor'] }}</title>
    <meta name="description" content="{{ $description ?? 'Guided rainforest walks, island tours and boat trips in Tobago with Ellis Clarke, a licensed tour guide with more than twenty years of experience. Book online or pay in cash at pickup.' }}">
    <meta name="theme-color" content="#0e1f19">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col" x-data="{ menu: false, search: false }" :class="menu && 'overflow-hidden'" @keydown.escape.window="menu = false; search = false">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-full focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold">Skip to content</a>

    @if ($announcement)
        <div class="bg-brand-600 text-white">
            <div class="wrap flex min-h-10 flex-wrap items-center justify-center gap-x-3 gap-y-1 py-2 text-center text-sm">
                <x-icon name="megaphone" class="hidden size-4 shrink-0 sm:block" />
                <p class="font-medium">{{ $announcement->message }}</p>
                @if ($announcement->link_url)
                    <a href="{{ $announcement->link_url }}" class="inline-flex items-center gap-1 font-semibold underline decoration-white/50 underline-offset-4 hover:decoration-white">{{ $announcement->link_text }} <x-icon name="arrow-right" class="size-3.5" /></a>
                @endif
            </div>
        </div>
    @endif

    {{-- Utility bar --}}
    <div class="bg-ink text-white">
        <div class="wrap flex h-10 items-center justify-between gap-4 text-xs">
            <p class="flex min-w-0 items-center gap-2 text-white/70">
                <x-icon name="map-pin" class="size-3.5 shrink-0 text-brand-300" />
                <span class="truncate">{{ $site['location'] }} <span class="hidden sm:inline">&middot; Licensed guide, {{ $site['years'] }}+ years of experience</span></span>
            </p>
            <div class="flex shrink-0 items-center gap-5">
                <a href="tel:{{ $site['phone_href'] }}" class="hidden items-center gap-1.5 font-semibold transition hover:text-brand-300 sm:flex">
                    <x-icon name="phone" class="size-3.5" /> {{ $site['phone'] }}
                </a>
                <a href="{{ $site['links']['tripadvisor'] }}" target="_blank" rel="noopener" class="hidden text-white/80 transition hover:text-white md:inline">Tripadvisor</a>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="flex items-center gap-1 font-semibold transition hover:text-brand-300">
                    WhatsApp Ellis <x-icon name="arrow-up-right" class="size-3.5" />
                </a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-line/80 bg-paper/90 backdrop-blur-lg">
        <div class="wrap flex h-[4.5rem] items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $site['name'] }} home">
                <x-logo />
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Main">
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @focusout="if (! $el.contains($event.relatedTarget)) open = false">
                    <a href="{{ route('tours') }}" @focus="open = true"
                        class="flex items-center gap-1 rounded-full px-3.5 py-2 text-sm font-medium transition hover:bg-ink/5 {{ in_array($current, ['tours', 'tours.show']) ? 'text-brand-700' : 'text-ink' }}"
                        :aria-expanded="open">
                        Tours
                        <x-icon name="chevron-down" class="size-3.5 opacity-50 transition" ::class="open && 'rotate-180'" />
                    </a>
                    <div x-cloak x-show="open" x-transition.origin.top.duration.150ms class="absolute top-full left-1/2 w-80 -translate-x-1/2 pt-3">
                        <div class="rounded-2xl bg-white p-2 shadow-lift ring-1 ring-line">
                            @foreach ($categories as $slug => $category)
                                <a href="{{ route('tours') }}#{{ $slug }}" class="group flex items-center gap-3 rounded-xl p-2.5 transition hover:bg-paper">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-paper text-ink transition group-hover:bg-brand-600 group-hover:text-white">
                                        <x-icon :name="$category['icon']" class="size-[18px]" />
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold">{{ $category['name'] }}</span>
                                        <span class="block text-xs text-muted">{{ $category['eyebrow'] }}</span>
                                    </span>
                                </a>
                            @endforeach
                            <a href="{{ route('tours') }}" class="mt-1 flex items-center justify-between rounded-xl border-t border-line px-3 py-3 text-sm font-semibold transition hover:text-brand-700">
                                All tours and prices <x-icon name="arrow-right" class="size-4" />
                            </a>
                        </div>
                    </div>
                </div>
                @foreach ($links as [$route, $label, $active])
                    <a href="{{ route($route) }}" class="rounded-full px-3.5 py-2 text-sm font-medium transition hover:bg-ink/5 {{ in_array($current, $active) ? 'text-brand-700' : '' }}">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <button type="button" class="grid size-10 place-items-center rounded-full transition hover:bg-ink/5" @click="search = true; $nextTick(() => $refs.search.focus())" aria-label="Search">
                    <x-icon name="search" class="size-[18px]" />
                </button>
                <a href="{{ route('book') }}" class="btn btn-primary hidden !py-2.5 sm:inline-flex">Book a tour</a>
                <button type="button" class="grid size-10 place-items-center rounded-full bg-ink text-white lg:hidden" @click="menu = true" aria-label="Open menu">
                    <x-icon name="menu" class="size-[18px]" />
                </button>
            </div>
        </div>
    </header>

    {{-- Search overlay --}}
    <div x-cloak x-show="search" x-transition.opacity class="fixed inset-0 z-50 bg-ink/60 p-4 backdrop-blur-sm sm:p-10" @click.self="search = false" role="dialog" aria-modal="true" aria-label="Search">
        <form action="{{ route('search') }}" class="mx-auto mt-16 flex max-w-2xl items-center gap-3 rounded-2xl bg-white p-3 pl-5 shadow-lift">
            <x-icon name="search" class="size-5 shrink-0 text-muted" />
            <input x-ref="search" type="search" name="q" placeholder="Search tours and questions…" class="w-full border-0 bg-transparent py-2 text-lg outline-none placeholder:text-muted/60" required>
            <button class="btn btn-dark">Search</button>
        </form>
    </div>

    {{-- Mobile menu --}}
    <div x-cloak x-show="menu" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-ink/50" @click="menu = false"></div>
        <div x-show="menu" x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col overflow-y-auto bg-paper shadow-lift">
            <div class="flex h-[4.5rem] shrink-0 items-center justify-between border-b border-line px-5">
                <x-logo />
                <button type="button" class="grid size-10 place-items-center rounded-full bg-ink text-white" @click="menu = false" aria-label="Close menu">
                    <x-icon name="x" class="size-[18px]" />
                </button>
            </div>
            <div class="flex-1 space-y-6 px-5 py-6">
                <div>
                    <p class="mb-2 text-xs font-semibold tracking-[0.16em] text-muted uppercase">Tours</p>
                    <div class="space-y-1">
                        @foreach ($categories as $slug => $category)
                            <a href="{{ route('tours') }}#{{ $slug }}" @click="menu = false" class="flex items-center gap-3 rounded-xl bg-white p-3 ring-1 ring-line/70">
                                <x-icon :name="$category['icon']" class="size-5 text-brand-700" />
                                <span class="text-sm font-semibold">{{ $category['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($links as [$route, $label])
                        <a href="{{ route($route) }}" class="btn btn-light">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="space-y-2 border-t border-line p-5">
                <a href="{{ route('book') }}" class="btn btn-primary w-full">Book a tour</a>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><x-icon name="message-circle" class="size-4" /> WhatsApp {{ $site['phone'] }}</a>
            </div>
        </div>
    </div>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="relative bg-ink text-white">
        <div class="gilt h-px w-full"></div>
        <div class="wrap py-16">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ $site['name'] }}" width="640" height="640" class="-ml-3 size-36" loading="lazy">
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/60">{{ $site['intro'] }}</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-onDark !py-2.5"><x-icon name="message-circle" class="size-4" /> WhatsApp</a>
                        <a href="tel:{{ $site['phone_href'] }}" class="btn btn-onDark !py-2.5"><x-icon name="phone" class="size-4" /> {{ $site['phone'] }}</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-7">
                    @foreach ([
                        'Tours' => [
                            ...collect($categories)->map(fn ($category, $slug) => [$category['name'], route('tours').'#'.$slug])->values()->all(),
                            ['All tours and prices', route('tours')],
                            ['Book a tour', route('book')],
                            ['Groups and schools', route('book').'#form-group'],
                        ],
                        'Ellis Tours' => [
                            ['About Ellis', route('about')],
                            ['Gallery', route('gallery')],
                            ['Reviews', route('reviews')],
                            ['Questions and answers', route('faq')],
                            ['Contact', route('contact')],
                        ],
                        'Connect' => [
                            ['Instagram', $site['links']['instagram']],
                            ['Facebook', $site['links']['facebook']],
                            ['TikTok', $site['links']['tiktok']],
                            ['Tripadvisor', $site['links']['tripadvisor']],
                            [$site['phone'], 'tel:'.$site['phone_href']],
                            [$site['email'], 'mailto:'.$site['email']],
                        ],
                    ] as $heading => $items)
                        <div class="min-w-0">
                            <h3 class="font-sans text-xs font-semibold tracking-[0.16em] text-white/40 uppercase">{{ $heading }}</h3>
                            <ul class="mt-4 space-y-2.5 text-sm">
                                @foreach ($items as [$text, $url])
                                    <li><a href="{{ $url }}" @if (str_starts_with($url, 'https://www.')) target="_blank" rel="noopener" @endif class="break-words text-white/75 transition hover:text-white">{{ $text }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-14 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $site['name'] }} &middot; {{ $site['address'] }}</p>
                <p class="flex flex-wrap gap-x-5 gap-y-1">
                    <a href="{{ route('login') }}" class="hover:text-white">Staff sign in</a>
                    <span>Website concept by Code Canvas Consultants Ltd.</span>
                </p>
            </div>
        </div>
    </footer>

    {{-- WhatsApp shortcut: the quickest way to reach Ellis, who is often out guiding --}}
    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="Chat with Ellis on WhatsApp"
        class="fixed right-4 bottom-4 z-30 flex items-center gap-2 rounded-full bg-emerald-700 py-3 pr-5 pl-4 text-sm font-semibold text-white shadow-lift transition hover:bg-emerald-800 sm:right-6 sm:bottom-6">
        <x-icon name="message-circle" class="size-5" /> <span class="hidden sm:inline">WhatsApp Ellis</span><span class="sm:hidden">Chat</span>
    </a>

    {{-- Cookie notice --}}
    <div x-cloak x-show="show" x-transition
        x-data="{
            show: false,
            init() { try { this.show = ! localStorage.getItem('ett-cookies') } catch (e) { this.show = true } },
            accept() { this.show = false; try { localStorage.setItem('ett-cookies', '1') } catch (e) {} },
        }"
        class="fixed bottom-20 left-4 z-40 flex max-w-md flex-col gap-4 rounded-2xl bg-white p-5 shadow-lift ring-1 ring-line right-4 sm:right-auto sm:bottom-6 sm:left-6 sm:flex-row sm:items-center">
        <p class="text-sm text-muted">We use cookies to keep this site working and to remember your preferences.</p>
        <button type="button" class="btn btn-dark shrink-0" @click="accept()">I accept</button>
    </div>
</body>
</html>
