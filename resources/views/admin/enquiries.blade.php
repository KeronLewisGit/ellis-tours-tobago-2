<x-admin title="Bookings and messages">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <label class="min-w-56 flex-1">
            <span class="label">Search</span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, email or reference" class="input">
        </label>
        <label>
            <span class="label">Type</span>
            <select name="type" class="input" onchange="this.form.submit()">
                <option value="">All types</option>
                @foreach (\App\Models\Enquiry::TYPES as $type => $meta)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="label">Status</span>
            <select name="status" class="input" onchange="this.form.submit()">
                <option value="">Any status</option>
                @foreach (\App\Models\Enquiry::STATUSES as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-dark">Filter</button>
        @if (request()->hasAny(['q', 'type', 'status']))
            <a href="{{ route('admin.enquiries') }}" class="btn btn-ghost">Clear</a>
        @endif
    </form>

    <div class="card mt-6 overflow-x-auto !p-0">
        <table class="table-clean min-w-[46rem]">
            <thead>
                <tr><th>Reference</th><th>From</th><th>Type</th><th>Summary</th><th>Received</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($enquiries as $enquiry)
                    <tr class="group cursor-pointer transition hover:bg-paper" onclick="window.location='{{ route('admin.enquiry', $enquiry) }}'">
                        <td><a href="{{ route('admin.enquiry', $enquiry) }}" class="font-mono text-xs font-semibold group-hover:text-brand-700">{{ $enquiry->reference }}</a></td>
                        <td>
                            <p class="font-semibold">{{ $enquiry->name }} @if ($enquiry->is_sample)<span class="ml-1 rounded bg-paper-deep px-1.5 py-0.5 text-[10px] font-semibold text-muted uppercase">Sample</span>@endif</p>
                            <p class="text-xs text-muted">{{ $enquiry->email }}</p>
                        </td>
                        <td class="whitespace-nowrap"><span class="inline-flex items-center gap-2"><x-icon :name="$enquiry->typeIcon()" class="size-4 text-muted" /> {{ $enquiry->typeLabel() }}</span></td>
                        <td class="max-w-56 truncate text-muted">{{ $enquiry->summary }}</td>
                        <td class="whitespace-nowrap text-muted">{{ $enquiry->created_at->format('j M, g:ia') }}</td>
                        <td><x-status-badge :enquiry="$enquiry" /></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="!py-14 text-center text-muted">Nothing matches these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $enquiries->links() }}</div>
</x-admin>
