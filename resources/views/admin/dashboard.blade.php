<x-admin title="Dashboard">
    <x-slot:actions>
        <a href="{{ route('admin.enquiries', ['status' => 'new']) }}" class="btn btn-dark !py-2.5">Review new requests</a>
    </x-slot:actions>

    <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['New', $newCount, 'Waiting for a first reply', 'inbox', 'text-brand-600', route('admin.enquiries', ['status' => 'new'])],
            ['Open', $open, 'New or in progress', 'clock', 'text-sky-600', route('admin.enquiries')],
            ['Last 7 days', $thisWeek, 'Requests and messages received', 'trending-up', 'text-emerald-600', route('admin.enquiries')],
            ['Reviews to approve', $pendingReviews, 'Sent from the website', 'star', 'text-muted', route('admin.reviews')],
        ] as [$label, $value, $hint, $icon, $colour, $url])
            <a href="{{ $url }}" class="card block !p-6 transition hover:shadow-lift">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-muted">{{ $label }}</dt>
                    <x-icon :name="$icon" class="size-5 {{ $colour }}" />
                </div>
                <dd class="mt-2 font-display text-5xl font-semibold">{{ $value }}</dd>
                <p class="mt-1 text-xs text-muted">{{ $hint }}</p>
            </a>
        @endforeach
    </dl>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="card !p-0 xl:col-span-2">
            <div class="flex items-center justify-between p-6 pb-4">
                <h2 class="font-display text-2xl font-semibold">Recent requests</h2>
                <a href="{{ route('admin.enquiries') }}" class="link-arrow">View all <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <ul class="divide-y divide-line border-t border-line">
                @forelse ($recent as $enquiry)
                    <li>
                        <a href="{{ route('admin.enquiry', $enquiry) }}" class="flex items-center gap-4 px-6 py-4 transition hover:bg-paper">
                            <span class="icon-tile !size-10 !rounded-xl"><x-icon :name="$enquiry->typeIcon()" class="size-[18px]" /></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold">{{ $enquiry->name }} <span class="font-normal text-muted">&middot; {{ $enquiry->summary }}</span></p>
                                <p class="text-xs text-muted">{{ $enquiry->typeLabel() }} &middot; {{ $enquiry->created_at->diffForHumans() }}</p>
                            </div>
                            <x-status-badge :enquiry="$enquiry" />
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-10 text-center text-sm text-muted">Nothing yet. Booking requests and messages from the website will appear here.</li>
                @endforelse
            </ul>
        </section>

        <div class="space-y-6">
            <section class="card !p-6">
                <h2 class="font-display text-2xl font-semibold">By type</h2>
                <ul class="mt-4 space-y-1">
                    @foreach ($byType as $type => $row)
                        <li>
                            <a href="{{ route('admin.enquiries', ['type' => $type]) }}" class="flex items-center gap-3 rounded-xl px-2 py-2 text-sm transition hover:bg-paper">
                                <x-icon :name="$row['meta']['icon']" class="size-[18px] text-muted" />
                                <span class="flex-1">{{ $row['meta']['label'] }}</span>
                                @if ($row['new'])
                                    <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700">{{ $row['new'] }} new</span>
                                @endif
                                <span class="w-6 text-right font-semibold tabular-nums">{{ $row['total'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="card !p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-display text-2xl font-semibold">Next blocked dates</h2>
                    <a href="{{ route('admin.dates') }}" class="link-arrow">Manage</a>
                </div>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse ($dates as $date)
                        <li class="flex items-center justify-between gap-3">
                            <span class="flex items-center gap-2.5"><x-icon name="{{ $date->status === 'booked' ? 'calendar-x' : 'calendar-days' }}" class="size-4 {{ $date->status === 'booked' ? 'text-rose-600' : 'text-amber-600' }}" /> {{ $date->date->format('D j M Y') }}</span>
                            <span class="text-xs text-muted">{{ $date->statusLabel() }}</span>
                        </li>
                    @empty
                        <li class="text-muted">No dates marked. Every date shows as open on the website.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-admin>
