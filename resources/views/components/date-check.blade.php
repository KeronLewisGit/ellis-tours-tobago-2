@props(['booked', 'dark' => false, 'tour' => null])
{{-- Live availability: reads the dates Ellis has marked in the admin calendar. --}}
<div x-data="dateChecker(@js((object) $booked))" {{ $attributes }}>
    <label for="check-date" @class(['mb-1.5 block text-sm font-medium', 'text-white/80' => $dark])>When would you like to go?</label>
    <div class="flex flex-wrap gap-2">
        <input id="check-date" type="date" x-model="date" :min="today" class="input max-w-52 {{ $dark ? '!bg-white/10 !text-white !ring-white/20 [color-scheme:dark]' : '' }}">
        <a :href="'{{ route('book', array_filter(['tour' => $tour])) }}' + (date && status !== 'past' ? '{{ $tour ? '&' : '?' }}date=' + date : '')" class="btn btn-primary">Check &amp; book <x-icon name="arrow-right" class="size-4" /></a>
    </div>
    <div class="mt-3 min-h-6 text-sm" aria-live="polite">
        <p x-cloak x-show="status === 'open'" @class(['flex items-center gap-2 font-medium', 'text-emerald-300' => $dark, 'text-emerald-700' => ! $dark])>
            <x-icon name="calendar-check" class="size-4 shrink-0" /> <span><span x-text="label"></span> is open. Book now to hold it.</span>
        </p>
        <p x-cloak x-show="status === 'limited'" @class(['flex items-center gap-2 font-medium', 'text-amber-300' => $dark, 'text-amber-700' => ! $dark])>
            <x-icon name="calendar-days" class="size-4 shrink-0" /> <span><span x-text="label"></span> has limited spaces. Ask us soon.</span>
        </p>
        <p x-cloak x-show="status === 'booked'" @class(['flex items-center gap-2 font-medium', 'text-rose-300' => $dark, 'text-rose-700' => ! $dark])>
            <x-icon name="calendar-x" class="size-4 shrink-0" /> <span><span x-text="label"></span> is fully booked. Try another day or message us.</span>
        </p>
        <p x-cloak x-show="status === 'past'" @class(['text-white/60' => $dark, 'text-muted' => ! $dark])>Choose a date in the future.</p>
        <p x-show="! status" @class(['text-white/50' => $dark, 'text-muted' => ! $dark])>Pick a date to see Ellis&rsquo;s calendar.</p>
    </div>
</div>
