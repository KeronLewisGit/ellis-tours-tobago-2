<x-layout title="Search">
    <x-page-hero eyebrow="Search" :title="$query !== '' ? 'Results for “'.$query.'”' : 'Search the site'">
        <form action="{{ route('search') }}" class="flex w-full max-w-xl items-center gap-2 rounded-full bg-white p-1.5 pl-5">
            <x-icon name="search" class="size-5 shrink-0 text-muted" />
            <input type="search" name="q" value="{{ $query }}" placeholder="Search tours and questions…" class="w-full border-0 bg-transparent py-2 text-ink outline-none placeholder:text-muted/60" required>
            <button class="btn btn-dark">Search</button>
        </form>
    </x-page-hero>

    <section class="section">
        <div class="wrap max-w-3xl">
            @if ($query === '')
                <p class="lead">Try “waterfall”, “hummingbird”, “Nylon Pool”, “lunch” or “cruise”.</p>
            @elseif ($results->isEmpty())
                <div class="card text-center">
                    <x-icon name="search-x" class="mx-auto size-10 text-muted" />
                    <h2 class="h-card mt-4">Nothing matched “{{ $query }}”.</h2>
                    <p class="mt-2 text-sm text-muted">Try a different word, or <a href="{{ route('contact') }}" class="link">ask Ellis</a>.</p>
                </div>
            @else
                <p class="text-sm text-muted">{{ $results->count() }} {{ Str::plural('result', $results->count()) }}</p>
                <ul class="mt-4 space-y-3">
                    @foreach ($results as $result)
                        <li>
                            <a href="{{ $result['url'] }}" class="card group block !p-6 transition hover:shadow-lift">
                                <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">{{ $result['type'] }}</span>
                                <h2 class="mt-3 font-display text-xl font-semibold group-hover:text-brand-700">{{ $result['title'] }}</h2>
                                <p class="mt-1 text-sm text-muted">{{ $result['text'] }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
</x-layout>
