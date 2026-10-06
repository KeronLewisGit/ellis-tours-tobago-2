@php
    $editing = $tour->exists;
    $images = $tour->images();
@endphp
<x-admin :title="$editing ? $tour->title : 'Add a tour'">
    <x-slot:actions>
        @if ($editing)
            <a href="{{ route('tours.show', $tour) }}" target="_blank" class="btn btn-light !py-2.5">View on website <x-icon name="arrow-up-right" class="size-4" /></a>
        @endif
        <a href="{{ route('admin.tours') }}" class="btn btn-light !py-2.5"><x-icon name="arrow-left" class="size-4" /> Tours</a>
    </x-slot:actions>

    <form method="POST" action="{{ $editing ? route('admin.tours.update', $tour) : route('admin.tours.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-3">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="space-y-6 xl:col-span-2">
            <section class="card">
                <h2 class="font-display text-2xl font-semibold">The tour</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <x-field name="title" label="Name" required :value="$tour->title" placeholder="e.g. 3-Hour Rainforest Walk" class="sm:col-span-2" />
                    <x-field name="category" type="select" label="Type of tour" required :options="collect(config('site.categories'))->map->name->all()" :value="$tour->category" placeholder="Choose one" />
                    <x-field name="duration" label="Length" :value="$tour->duration" placeholder="e.g. Full day" />
                    <x-field name="summary" label="One-line summary" required :value="$tour->summary" maxlength="255" class="sm:col-span-2" hint="Shown on the tour card and under the title." />
                    <x-field name="body" type="textarea" label="Description" :value="$tour->body" rows="7" class="sm:col-span-2" hint="Leave a blank line between paragraphs." />
                </div>
            </section>

            <section class="card">
                <h2 class="font-display text-2xl font-semibold">The day</h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <x-field name="pickup" label="Hotel pickup" :value="$tour->pickup" placeholder="e.g. 9:00am" />
                    <x-field name="dropoff" label="Back by" :value="$tour->dropoff" placeholder="e.g. 4–5pm" />
                    <x-field name="distance" label="Walk" :value="$tour->distance" placeholder="e.g. 5 km · low intensity" />
                    <x-field name="includes" type="textarea" label="What is included" :value="$tour->linesText('includes')" rows="7" class="sm:col-span-3" hint="One item per line." />
                    <x-field name="bring" type="textarea" label="What to wear and carry" :value="$tour->linesText('bring')" rows="6" class="sm:col-span-3" hint="One item per line." />
                    <x-field name="notes" type="textarea" label="Good to know" :value="$tour->linesText('notes')" rows="3" class="sm:col-span-3" hint="One note per line, such as “Lunch is not included.”" />
                </div>
            </section>

            <section class="card">
                <h2 class="font-display text-2xl font-semibold">Photos</h2>
                @if ($images)
                    <p class="mt-1 text-sm text-muted">The first photo is the cover. Tick a photo to remove it when you save.</p>
                    <div class="mt-5 grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($images as $path)
                            <label class="group relative block cursor-pointer overflow-hidden rounded-2xl bg-paper-deep ring-1 ring-line">
                                <img src="{{ asset($path) }}" alt="" class="aspect-square w-full object-cover" loading="lazy">
                                @if ($loop->first)
                                    <span class="absolute top-2 left-2 rounded-full bg-ink px-2 py-0.5 text-[10px] font-semibold text-white uppercase">Cover</span>
                                @endif
                                <span class="absolute right-2 bottom-2 flex items-center gap-1.5 rounded-full bg-white/95 px-2 py-1 text-[11px] font-semibold shadow-sm">
                                    <input type="checkbox" name="remove[]" value="{{ $path }}" class="size-3.5 accent-rose-600"> Remove
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <label class="block" x-data="fileField()">
                        <span class="label">{{ $images ? 'Replace the cover' : 'Cover photo' }}</span>
                        <span class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-line bg-paper p-4 text-sm transition hover:border-ink/40">
                            <x-icon name="upload" class="size-5 shrink-0 text-brand-700" />
                            <span class="min-w-0 truncate font-semibold" x-text="name || 'Choose a photo'">Choose a photo</span>
                            <input type="file" name="cover_file" class="sr-only" accept=".jpg,.jpeg,.png,.webp" @change="pick($event)">
                        </span>
                    </label>
                    <label class="block" x-data="fileField()">
                        <span class="label">Add gallery photos</span>
                        <span class="flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-line bg-paper p-4 text-sm transition hover:border-ink/40">
                            <x-icon name="images" class="size-5 shrink-0 text-brand-700" />
                            <span class="min-w-0 truncate font-semibold" x-text="name || 'Choose up to 12 photos'">Choose up to 12 photos</span>
                            <input type="file" name="photos[]" multiple class="sr-only" accept=".jpg,.jpeg,.png,.webp" @change="pick($event)">
                        </span>
                    </label>
                </div>
                <p class="hint">JPG, PNG or WebP, up to 6 MB each.</p>
            </section>
        </div>

        <div class="space-y-6">
            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Publishing</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="is_published" value="1" class="mt-0.5 size-4 rounded accent-brand-600" @checked(old('is_published', $tour->is_published))>
                        <span><strong class="block font-semibold">Show on the website</strong><span class="text-muted">Untick to keep it as a draft.</span></span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="is_available" value="1" class="mt-0.5 size-4 rounded accent-brand-600" @checked(old('is_available', $tour->is_available))>
                        <span><strong class="block font-semibold">Taking bookings</strong><span class="text-muted">Untick when the tour is not running. It stays on the site, marked “not running”.</span></span>
                    </label>
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="is_featured" value="1" class="mt-0.5 size-4 rounded accent-brand-600" @checked(old('is_featured', $tour->is_featured))>
                        <span><strong class="block font-semibold">Feature on the home page</strong><span class="text-muted">Up to six featured tours are shown.</span></span>
                    </label>
                </div>
                <x-field name="sort_order" type="number" label="Position in the list" :value="$tour->sort_order ?? 0" min="0" max="9999" class="mt-5" hint="Lower numbers come first." />
                <button class="btn btn-primary mt-6 w-full">{{ $editing ? 'Save changes' : 'Add tour' }}</button>
            </section>

            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Prices</h2>
                <p class="mt-1 text-sm text-muted">US dollars, per person. Leave “private” empty for a tour with one standard rate.</p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-field name="price_group" type="number" label="Group" :value="$tour->price_group" min="1" max="5000" inputmode="numeric" />
                    <x-field name="price_private" type="number" label="Private" :value="$tour->price_private" min="1" max="5000" inputmode="numeric" />
                </div>
            </section>

            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">Online payment links</h2>
                <p class="mt-1 text-sm text-muted">The booking page for each rate in your online booking system. Leave empty to hide the “Pay online” button.</p>
                <x-field name="booking_group_url" type="url" label="Group or standard rate" :value="$tour->booking_group_url" placeholder="https://…" class="mt-4" />
                <x-field name="booking_private_url" type="url" label="Private rate" :value="$tour->booking_private_url" placeholder="https://…" class="mt-4" />
            </section>
        </div>
    </form>

    @if ($editing)
        <form method="POST" action="{{ route('admin.tours.destroy', $tour) }}" class="mt-6" onsubmit="return confirm('Remove “{{ $tour->title }}”? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-ghost !text-rose-700"><x-icon name="trash-2" class="size-4" /> Delete this tour</button>
        </form>
    @endif
</x-admin>
