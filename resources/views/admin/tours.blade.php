<x-admin title="Tours">
    <x-slot:actions>
        <a href="{{ route('admin.tours.create') }}" class="btn btn-primary !py-2.5"><x-icon name="plus" class="size-4" /> Add a tour</a>
    </x-slot:actions>

    <p class="max-w-2xl text-sm text-muted">Tours shown on the <a href="{{ route('tours') }}" target="_blank" class="link">Tours page</a>, in this order. Featured tours also appear on the home page. A tour marked “not running” stays visible but cannot be booked.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($tours as $tour)
            <a href="{{ route('admin.tours.edit', $tour) }}" class="card group flex gap-4 !p-4 transition hover:shadow-lift">
                <div class="size-24 shrink-0 overflow-hidden rounded-2xl bg-paper-deep">
                    @if ($tour->cover)
                        <img src="{{ asset($tour->cover) }}" alt="" class="size-full object-cover" loading="lazy">
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate font-display text-lg font-semibold group-hover:text-brand-700">{{ $tour->title }}</p>
                    <p class="mt-0.5 text-xs text-muted">
                        {{ $tour->categoryLabel() }}
                        @foreach ($tour->rates() as $rate)
                            &middot; {{ $rate['label'] }} {{ config('site.currency') }}{{ $rate['price'] }}
                        @endforeach
                    </p>
                    <div class="mt-3 flex flex-wrap gap-1.5">
                        @if ($tour->is_published)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Live</span>
                        @else
                            <span class="rounded-full bg-paper px-2.5 py-1 text-xs font-semibold text-muted ring-1 ring-line">Draft</span>
                        @endif
                        @unless ($tour->is_available)
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">Not running</span>
                        @endunless
                        @if ($tour->is_featured)
                            <span class="rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700">Featured</span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <p class="card text-center text-sm text-muted sm:col-span-2 xl:col-span-3">No tours yet. Add your first one.</p>
        @endforelse
    </div>
</x-admin>
