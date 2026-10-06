<x-admin title="Announcements">
    <div class="mb-6 overflow-hidden rounded-2xl ring-1 ring-line">
        <p class="bg-white px-4 py-2 text-xs font-semibold tracking-[0.16em] text-muted uppercase">Showing on the website now</p>
        @if ($live)
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 bg-brand-600 px-4 py-2.5 text-center text-sm text-white">
                <x-icon name="megaphone" class="size-4" />
                <p class="font-medium">{{ $live->message }}</p>
                @if ($live->link_url)
                    <span class="font-semibold underline underline-offset-4">{{ $live->link_text }}</span>
                @endif
            </div>
        @else
            <p class="bg-paper px-4 py-3 text-center text-sm text-muted">No announcement is showing.</p>
        @endif
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card !p-0 xl:col-span-2">
            <div class="p-6 pb-4">
                <h2 class="font-display text-2xl font-semibold">All announcements</h2>
                <p class="mt-1 text-sm text-muted">One notice shows at a time. A notice with an end date takes priority over an open-ended one, so seasonal notices and hiring calls appear automatically and then step aside.</p>
            </div>
            <ul class="divide-y divide-line border-t border-line">
                @forelse ($announcements as $announcement)
                    <li class="flex flex-wrap items-center gap-4 px-6 py-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold">{{ $announcement->message }}</p>
                            <p class="mt-0.5 text-xs text-muted">
                                {{ $announcement->window() }}
                                @if ($announcement->link_url) &middot; {{ $announcement->link_text }} &rarr; {{ $announcement->link_url }} @endif
                            </p>
                        </div>
                        @if ($live?->is($announcement))
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Showing now</span>
                        @elseif (! $announcement->is_active)
                            <span class="rounded-full bg-paper px-2.5 py-1 text-xs font-semibold text-muted ring-1 ring-line">Off</span>
                        @elseif ($announcement->ends_on?->lt(today()))
                            <span class="rounded-full bg-paper px-2.5 py-1 text-xs font-semibold text-muted ring-1 ring-line">Ended</span>
                        @else
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800">Queued</span>
                        @endif
                        <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-light !px-3.5 !py-2">{{ $announcement->is_active ? 'Switch off' : 'Switch on' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Remove this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button class="grid size-9 place-items-center rounded-full text-muted transition hover:bg-rose-50 hover:text-rose-700" aria-label="Remove announcement"><x-icon name="trash-2" class="size-4" /></button>
                        </form>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-muted">No announcements yet.</li>
                @endforelse
            </ul>
        </section>

        <section class="card !p-6">
            <h2 class="font-display text-2xl font-semibold">New announcement</h2>
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-4 space-y-4">
                @csrf
                <label class="block"><span class="label">Message</span><input type="text" name="message" value="{{ old('message') }}" maxlength="140" class="input" placeholder="e.g. Christmas week is filling up. Book early." required></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block"><span class="label">Link text</span><input type="text" name="link_text" value="{{ old('link_text') }}" maxlength="40" class="input" placeholder="Book a tour"></label>
                    <label class="block"><span class="label">Link</span><input type="text" name="link_url" value="{{ old('link_url') }}" class="input" placeholder="/book"></label>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block"><span class="label">Show from</span><input type="date" name="starts_on" value="{{ old('starts_on') }}" class="input"></label>
                    <label class="block"><span class="label">Until</span><input type="date" name="ends_on" value="{{ old('ends_on') }}" class="input"></label>
                </div>
                <p class="hint">Leave the dates empty to show the notice until you switch it off.</p>
                <button class="btn btn-primary w-full"><x-icon name="megaphone" class="size-4" /> Publish</button>
            </form>
        </section>
    </div>
</x-admin>
