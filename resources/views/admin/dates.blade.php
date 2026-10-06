<x-admin title="Calendar">
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card !p-0 xl:col-span-2">
            <div class="p-6 pb-4">
                <h2 class="font-display text-2xl font-semibold">Blocked dates</h2>
                <p class="mt-1 text-sm text-muted">Visitors can check a date on the <a href="{{ route('home') }}" target="_blank" class="link">home page</a> and on the <a href="{{ route('book') }}" target="_blank" class="link">booking page</a>. Any date not listed here shows as open. Notes are for staff only.</p>
            </div>
            <ul class="divide-y divide-line border-t border-line">
                @forelse ($upcoming as $date)
                    <li class="flex flex-wrap items-center gap-4 px-6 py-4">
                        <div class="w-14 shrink-0 rounded-xl bg-paper py-1.5 text-center ring-1 ring-line">
                            <p class="text-[10px] font-semibold tracking-wider text-muted uppercase">{{ $date->date->format('M') }}</p>
                            <p class="font-display text-2xl leading-none font-semibold">{{ $date->date->format('j') }}</p>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold">{{ $date->date->format('l j F Y') }}</p>
                            <p class="mt-0.5 text-xs text-muted">{{ $date->note ?: 'No note' }}</p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $date->status === 'booked' ? 'bg-rose-50 text-rose-800' : 'bg-amber-50 text-amber-800' }}">{{ $date->statusLabel() }}</span>
                        <form method="POST" action="{{ route('admin.dates.destroy', $date) }}" onsubmit="return confirm('Open this date up again?')">
                            @csrf
                            @method('DELETE')
                            <button class="grid size-9 place-items-center rounded-full text-muted transition hover:bg-rose-50 hover:text-rose-700" aria-label="Remove {{ $date->date->format('j F Y') }}"><x-icon name="trash-2" class="size-4" /></button>
                        </form>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-sm text-muted">No upcoming dates are marked. Every date shows as open.</li>
                @endforelse
            </ul>
            @if ($past->isNotEmpty())
                <p class="border-t border-line px-6 py-4 text-xs text-muted">Recently passed: {{ $past->map(fn ($date) => $date->date->format('j M'))->implode(', ') }}</p>
            @endif
        </section>

        <section class="card !p-6">
            <h2 class="font-display text-2xl font-semibold">Mark a date</h2>
            <form method="POST" action="{{ route('admin.dates.store') }}" class="mt-4 space-y-4">
                @csrf
                <label class="block"><span class="label">Date</span><input type="date" name="date" value="{{ old('date') }}" class="input" required></label>
                <label class="block">
                    <span class="label">Show it as</span>
                    <select name="status" class="input">
                        @foreach (\App\Models\BookedDate::STATUSES as $status => $label)
                            <option value="{{ $status }}" @selected(old('status') === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block"><span class="label">Note</span><input type="text" name="note" value="{{ old('note') }}" maxlength="160" class="input" placeholder="e.g. Wedding at the Hyatt"></label>
                <p class="hint">Marking a date that is already listed updates it.</p>
                <button class="btn btn-primary w-full"><x-icon name="plus" class="size-4" /> Save date</button>
            </form>
        </section>
    </div>
</x-admin>
