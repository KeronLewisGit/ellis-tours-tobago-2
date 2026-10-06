<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Staff sign in · Ellis Tours Tobago</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-ink p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <img src="{{ asset('images/photos/img_3133.webp') }}" alt="" class="absolute inset-0 size-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/60 to-ink/30"></div>
        <img src="{{ asset('images/logo.png') }}" alt="Ellis Tours Tobago" class="relative -ml-4 size-36 self-start">
        <div class="relative">
            <p class="eyebrow !text-brand-300">{{ config('site.tagline') }}</p>
            <h1 class="mt-3 font-display text-5xl leading-[1.05] font-semibold tracking-tight">Every booking,<br>one inbox.</h1>
            <p class="mt-4 max-w-md text-white/70">Booking requests, group enquiries and messages from the website, plus your calendar, tours, prices, reviews and site announcements.</p>
        </div>
        <div class="gilt relative h-px w-40"></div>
    </div>

    <div class="flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-sm">
            <x-logo class="mb-10 lg:hidden" />
            <h2 class="font-display text-4xl font-semibold tracking-tight">Staff sign in</h2>
            <p class="mt-2 text-sm text-muted">Sign in to manage the website.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-4">
                @csrf
                <x-field name="email" type="email" label="Email" required autofocus autocomplete="username" :value="app()->isLocal() ? 'admin@ellistourstobago.com' : null" />
                <x-field name="password" type="password" label="Password" required autocomplete="current-password" />
                <label class="flex items-center gap-2.5 text-sm">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded accent-brand-600"> Keep me signed in
                </label>
                <button type="submit" class="btn btn-primary w-full !py-3.5 !text-base">Sign in</button>
            </form>

            <a href="{{ route('home') }}" class="link-arrow mt-8"><x-icon name="arrow-left" class="size-4" /> Back to the website</a>
        </div>
    </div>
</body>
</html>
