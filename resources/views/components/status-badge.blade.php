@props(['enquiry'])
<span @class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold whitespace-nowrap',
    'bg-brand-50 text-brand-700' => $enquiry->status === 'new',
    'bg-sky-50 text-sky-800' => $enquiry->status === 'in_progress',
    'bg-emerald-50 text-emerald-800' => $enquiry->status === 'booked',
    'bg-paper text-muted ring-1 ring-line' => $enquiry->status === 'closed',
])>
    <span @class([
        'size-1.5 rounded-full',
        'bg-brand-600' => $enquiry->status === 'new',
        'bg-sky-500' => $enquiry->status === 'in_progress',
        'bg-emerald-500' => $enquiry->status === 'booked',
        'bg-muted/50' => $enquiry->status === 'closed',
    ])></span>
    {{ $enquiry->statusLabel() }}
</span>
