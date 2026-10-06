<x-admin title="Reviews">
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card !p-0 xl:col-span-2">
            <div class="p-6 pb-4">
                <h2 class="font-display text-2xl font-semibold">Guest reviews</h2>
                <p class="mt-1 text-sm text-muted">Reviews sent from the <a href="{{ route('reviews') }}" target="_blank" class="link">Reviews page</a> wait here until you publish them. Published reviews also appear on the home page and on the matching tour.</p>
            </div>
            <ul class="divide-y divide-line border-t border-line">
                @forelse ($reviews as $review)
                    <li class="flex flex-wrap items-start gap-4 px-6 py-5">
                        <div class="min-w-0 flex-1">
                            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-semibold">
                                {{ $review->name }}
                                @if ($review->origin)<span class="font-normal text-muted">{{ $review->origin }}</span>@endif
                                @if ($review->rating)
                                    <span class="inline-flex items-center gap-1 text-brand-700"><x-icon name="star" class="size-3.5 fill-brand-400 text-brand-400" /> {{ $review->rating }}/5</span>
                                @endif
                            </p>
                            <p class="mt-0.5 text-xs text-muted">{{ $review->sourceLabel() }} @if ($review->tour_title)&middot; {{ $review->tour_title }}@endif &middot; {{ $review->created_at->format('j M Y') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-ink/80">{{ $review->body }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            @if ($review->is_published)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Published</span>
                            @else
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">Waiting</span>
                            @endif
                            <form method="POST" action="{{ route('admin.reviews.toggle', $review) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn {{ $review->is_published ? 'btn-light' : 'btn-primary' }} !px-3.5 !py-2">{{ $review->is_published ? 'Hide' : 'Publish' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Remove this review?')">
                                @csrf
                                @method('DELETE')
                                <button class="grid size-9 place-items-center rounded-full text-muted transition hover:bg-rose-50 hover:text-rose-700" aria-label="Remove review"><x-icon name="trash-2" class="size-4" /></button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-muted">No reviews yet.</li>
                @endforelse
            </ul>
        </section>

        <section class="card !p-6">
            <h2 class="font-display text-2xl font-semibold">Add a review</h2>
            <p class="mt-1 text-sm text-muted">For kind words you received somewhere else, such as Tripadvisor, Instagram or WhatsApp. Ask the guest before you publish them.</p>
            <form method="POST" action="{{ route('admin.reviews.store') }}" class="mt-4 space-y-4">
                @csrf
                <label class="block"><span class="label">Guest name</span><input type="text" name="name" value="{{ old('name') }}" maxlength="80" class="input" required></label>
                <label class="block"><span class="label">From</span><input type="text" name="origin" value="{{ old('origin') }}" maxlength="80" class="input" placeholder="e.g. United Kingdom"></label>
                <label class="block">
                    <span class="label">Tour</span>
                    <select name="tour_title" class="input">
                        <option value="">Not sure</option>
                        @foreach ($tours as $title)
                            <option value="{{ $title }}" @selected(old('tour_title') === $title)>{{ $title }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="label">Stars</span>
                        <select name="rating" class="input">
                            <option value="">None</option>
                            @foreach ([5, 4, 3, 2, 1] as $stars)
                                <option value="{{ $stars }}" @selected((int) old('rating') === $stars)>{{ $stars }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="label">Where from</span>
                        <select name="source" class="input">
                            @foreach (\App\Models\Review::SOURCES as $source => $label)
                                <option value="{{ $source }}" @selected(old('source', 'tripadvisor') === $source)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <label class="block"><span class="label">Review</span><textarea name="body" rows="5" maxlength="1500" class="input" required>{{ old('body') }}</textarea></label>
                <label class="block"><span class="label">Link to the original</span><input type="url" name="source_url" value="{{ old('source_url') }}" class="input" placeholder="https://… (optional)"></label>
                <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4" /> Publish review</button>
            </form>
        </section>
    </div>
</x-admin>
