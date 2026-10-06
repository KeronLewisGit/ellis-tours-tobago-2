@php
    $site = config('site');
    $bag = 'booking';
    $errorBag = $errors->getBag($bag);
    $failed = $errorBag->any();
    $old = fn (string $key, $default = null) => $failed ? old($key, $default) : $default;
    $requested = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) request('date')) ? request('date') : '';
    // Re-open the step that holds the first error.
    $stepFields = [['tour', 'rate', 'tour_date'], ['adults', 'children', 'pickup', 'payment', 'message'], ['name', 'email', 'phone', 'contact_method', 'heard']];
    $start = 0;
    foreach ($stepFields as $index => $fields) {
        if ($errorBag->hasAny($fields)) { $start = $index; break; }
    }
    $tourData = $tours->mapWithKeys(fn ($tour) => [$tour->slug => [
        'title' => $tour->title,
        'pickup' => $tour->pickup,
        'dropoff' => $tour->dropoff,
        'rates' => $tour->rates(),
    ]]);
    $initial = [
        'tour' => $old('tour', $selected?->slug ?? ''),
        'rate' => $old('rate', in_array(request('rate'), array_keys(\App\Models\Tour::RATES), true) ? request('rate') : ''),
        'adults' => $old('adults', 2),
        'children' => $old('children', 0),
    ];
    $groupBag = $errors->getBag('group');
    $oldGroup = fn (string $key, $default = null) => $groupBag->any() ? old($key, $default) : $default;
@endphp
<x-layout title="Book a tour" description="Check your date and book a Tobago tour with Ellis Tours. Pay online, or in cash at pickup.">
    <x-page-hero eyebrow="Book a tour" title="Reserve your day in Tobago."
        lead="Three short steps. Ellis confirms your pickup by WhatsApp or email, and you can pay online or in cash on the day." />

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12" x-data="bookingForm(@js($tourData), @js($initial))">
            <div class="lg:col-span-8">
                <form id="form-booking" method="POST" action="{{ route('enquiries.store', 'booking') }}"
                    class="card scroll-mt-28" x-data="stepper(3, {{ $start }})">
                    @csrf
                    <x-form-status type="booking" title="We have your booking request." message="Thank you. Ellis will confirm your pickup time and place by WhatsApp or email." />

                    {{-- Progress --}}
                    <ol class="mb-8 grid grid-cols-3 gap-2 text-xs font-semibold">
                        @foreach (['Tour and date', 'Your party', 'Your details'] as $label)
                            <li>
                                <button type="button" class="block w-full text-left" @click="{{ $loop->index }} < step && go({{ $loop->index }})" :class="{{ $loop->index }} < step ? 'cursor-pointer' : 'cursor-default'">
                                    <span class="block h-1 rounded-full transition" :class="step >= {{ $loop->index }} ? 'bg-brand-600' : 'bg-line'"></span>
                                    <span class="mt-2 block" :class="step >= {{ $loop->index }} ? 'text-ink' : 'text-muted'">{{ $loop->iteration }}. {{ $label }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ol>

                    {{-- Step 1 --}}
                    <div data-step="0" x-show="step === 0">
                        <h2 class="font-display text-2xl font-semibold">Which tour, and when?</h2>
                        <div class="mt-5 grid gap-5">
                            <x-field name="tour" type="select" label="Tour" :bag="$bag" required
                                :options="$tours->pluck('title', 'slug')->all()" placeholder="Choose a tour" x-model="tour" />

                            <fieldset x-cloak x-show="rates.length > 1">
                                <legend class="label">Group or private?</legend>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <template x-for="option in rates" :key="option.key">
                                        <label class="option">
                                            <input type="radio" name="rate" :value="option.key" x-model="rate" class="peer sr-only">
                                            <span class="justify-between">
                                                <span x-text="option.label"></span>
                                                <span class="font-semibold">{{ $site['currency'] }}<span x-text="option.price"></span> <span class="text-xs font-normal text-muted">per person</span></span>
                                            </span>
                                        </label>
                                    </template>
                                </div>
                                <p class="hint">Group tours may include other guests. Private tours are for your party only, with a flexible pickup time.</p>
                            </fieldset>

                            <div x-data="dateChecker(@js((object) $booked), @js($old('tour_date', $requested)))">
                                <label for="booking-tour_date" class="label">Tour date <span class="text-brand-600" aria-hidden="true">*</span></label>
                                <input type="date" id="booking-tour_date" name="tour_date" x-model="date" :min="today" required class="input sm:max-w-xs {{ $errorBag->has('tour_date') ? 'input-error' : '' }}">
                                @if ($errorBag->has('tour_date'))
                                    <p class="field-error">{{ $errorBag->first('tour_date') }}</p>
                                @endif
                                <div class="mt-2 text-sm" aria-live="polite">
                                    <p x-cloak x-show="status === 'open'" class="flex items-center gap-2 font-medium text-emerald-700"><x-icon name="calendar-check" class="size-4 shrink-0" /> <span><span x-text="label"></span> is open on Ellis&rsquo;s calendar.</span></p>
                                    <p x-cloak x-show="status === 'limited'" class="flex items-center gap-2 font-medium text-amber-700"><x-icon name="calendar-days" class="size-4 shrink-0" /> <span><span x-text="label"></span> has limited spaces. Send your request and we will confirm.</span></p>
                                    <p x-cloak x-show="status === 'booked'" class="flex items-center gap-2 font-medium text-rose-700"><x-icon name="calendar-x" class="size-4 shrink-0" /> <span><span x-text="label"></span> is fully booked. Please choose another day.</span></p>
                                    <p x-cloak x-show="status === 'past'" class="text-muted">Choose a date in the future.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div data-step="1" x-show="step === 1" x-cloak>
                        <h2 class="font-display text-2xl font-semibold">Who is coming, and where from?</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <x-field name="adults" type="number" label="Adults" :bag="$bag" required min="1" max="40" inputmode="numeric" x-model.number="adults" />
                            <x-field name="children" type="number" label="Children" :bag="$bag" min="0" max="40" inputmode="numeric" x-model.number="children" hint="Tell us their ages in the note below." />
                            <x-field name="pickup" label="Where are you staying?" :bag="$bag" required class="sm:col-span-2"
                                placeholder="Hotel, guesthouse or villa name and area" hint="Pickup and drop-off are included. Arriving by cruise ship? Write “cruise ship” and your docking time." />

                            <fieldset class="sm:col-span-2">
                                <legend class="label">How would you like to pay? <span class="text-brand-600" aria-hidden="true">*</span></legend>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($site['payment_methods'] as $method)
                                        <label class="option">
                                            <input type="radio" name="payment" value="{{ $method }}" class="peer sr-only" required @checked($old('payment', $site['payment_methods'][0]) === $method)>
                                            <span><x-icon name="{{ $loop->first ? 'banknote' : 'credit-card' }}" class="size-4 shrink-0 text-brand-600" /> {{ $method }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @if ($errorBag->has('payment'))
                                    <p class="field-error">{{ $errorBag->first('payment') }}</p>
                                @endif
                                <p class="hint">Choose &ldquo;Pay online&rdquo; and Ellis will reply with the online booking link for your tour and date.</p>
                            </fieldset>

                            <x-field name="message" type="textarea" label="Anything we should know?" :bag="$bag" class="sm:col-span-2" rows="4"
                                placeholder="Children’s ages, mobility, dietary needs, or something you especially want to see" />
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div data-step="2" x-show="step === 2" x-cloak>
                        <h2 class="font-display text-2xl font-semibold">How do we reach you?</h2>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <x-field name="name" label="Your name" :bag="$bag" required autocomplete="name" />
                            <x-field name="phone" type="tel" label="Phone or WhatsApp" :bag="$bag" required autocomplete="tel" placeholder="With country code" />
                            <x-field name="email" type="email" label="Email" :bag="$bag" required autocomplete="email" />
                            <x-field name="contact_method" type="select" label="Best way to reach you" :bag="$bag" required :options="$site['contact_methods']" value="WhatsApp" />
                            <x-field name="heard" type="select" label="How did you hear about us?" :bag="$bag" :options="$site['heard']" placeholder="Optional" class="sm:col-span-2" />
                        </div>
                    </div>

                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-6">
                        <button type="button" class="btn btn-light" x-show="step > 0" x-cloak @click="go(step - 1)"><x-icon name="arrow-left" class="size-4" /> Back</button>
                        <span x-show="step === 0"></span>
                        <button type="button" class="btn btn-dark" x-show="step < steps - 1" @click="next()">Continue <x-icon name="arrow-right" class="size-4" /></button>
                        <button type="submit" class="btn btn-primary" x-show="step === steps - 1" x-cloak>Send booking request <x-icon name="send" class="size-4" /></button>
                    </div>
                </form>
            </div>

            <aside class="space-y-5 lg:col-span-4">
                {{-- Live summary --}}
                <div class="card !p-6" data-reveal>
                    <h2 class="font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase">Your booking</h2>
                    <p class="mt-3 font-display text-2xl leading-tight font-semibold" x-text="current ? current.title : 'Choose a tour to begin'">Choose a tour to begin</p>
                    <dl class="mt-4 divide-y divide-line text-sm" x-cloak x-show="current">
                        <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Rate</dt><dd class="text-right font-semibold" x-text="selectedRate ? selectedRate.label : '–'"></dd></div>
                        <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Price per person</dt><dd class="text-right font-semibold">{{ $site['currency'] }}<span x-text="selectedRate ? selectedRate.price : '–'"></span></dd></div>
                        <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Guests</dt><dd class="text-right font-semibold" x-text="guests"></dd></div>
                        <div class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Hotel pickup</dt><dd class="text-right font-semibold" x-text="current?.pickup ?? '–'"></dd></div>
                        <div class="flex justify-between gap-4 py-2.5" x-show="current?.dropoff"><dt class="text-muted">Back by</dt><dd class="text-right font-semibold" x-text="current?.dropoff"></dd></div>
                    </dl>
                    <p class="mt-4 text-xs text-muted" x-cloak x-show="current">Ellis confirms your total when he replies. Nothing is charged when you send this request.</p>
                    <template x-if="selectedRate && selectedRate.url">
                        <a :href="selectedRate.url" target="_blank" rel="noopener" class="btn btn-light mt-4 w-full">Rather pay online now? <x-icon name="arrow-up-right" class="size-4" /></a>
                    </template>
                </div>

                <div class="rounded-3xl bg-ink p-6 text-white sm:p-8" data-reveal style="--reveal-delay: 100ms">
                    <p class="text-xs font-semibold tracking-[0.16em] text-white/50 uppercase">Prefer to talk?</p>
                    <p class="mt-3 font-display text-2xl font-semibold">Message Ellis on WhatsApp.</p>
                    <p class="mt-2 text-sm text-white/60">He is often out on tour, and replies between stops.</p>
                    <div class="mt-5 space-y-2">
                        <a href="{{ \App\Support\Content::whatsapp('Hi Ellis, I would like to book a tour.') }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><x-icon name="message-circle" class="size-4" /> WhatsApp {{ $site['phone'] }}</a>
                        <a href="tel:{{ $site['phone_href'] }}" class="btn btn-onDark w-full"><x-icon name="phone" class="size-4" /> Call</a>
                    </div>
                </div>

                <div class="card !p-6" data-reveal style="--reveal-delay: 160ms">
                    <h2 class="h-card">What happens next</h2>
                    <ol class="mt-4 space-y-4 text-sm">
                        @foreach (array_slice($site['process'], 1) as [$icon, $title, $text])
                            <li class="flex gap-3">
                                <x-icon :name="$icon" class="mt-0.5 size-[18px] shrink-0 text-brand-600" />
                                <span><strong class="block font-semibold">{{ $title }}</strong><span class="text-muted">{{ $text }}</span></span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>
        </div>
    </section>

    {{-- Groups --}}
    <section class="section border-t border-line bg-white">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5" data-reveal>
                <p class="eyebrow">Groups and custom days</p>
                <h2 class="h-section mt-4">Schools, cruise visitors and big families.</h2>
                <p class="lead mt-4">Tell us who is coming and what you would like to see, and Ellis will put a day together for you, with a price for the group.</p>
                <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex gap-3"><x-icon name="users" class="mt-0.5 size-[18px] shrink-0 text-brand-600" /> <span><strong class="font-semibold">School and youth groups.</strong> A day in the rainforest and around the island for a class or a club.</span></li>
                    <li class="flex gap-3"><x-icon name="ship" class="mt-0.5 size-[18px] shrink-0 text-brand-600" /> <span><strong class="font-semibold">Cruise ship visitors.</strong> Send your docking and all-aboard times and we will fit the tour to them.</span></li>
                    <li class="flex gap-3"><x-icon name="route" class="mt-0.5 size-[18px] shrink-0 text-brand-600" /> <span><strong class="font-semibold">Your own route.</strong> Mix a waterfall, a beach and a lunch stop into one private day.</span></li>
                </ul>
            </div>
            <div class="lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
                <form id="form-group" method="POST" action="{{ route('enquiries.store', 'group') }}" class="card scroll-mt-28 bg-paper">
                    @csrf
                    <x-form-status type="group" title="We have your group request." message="Thank you. Ellis will come back to you with ideas and a price." />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-field name="group_type" type="select" label="Type of group" bag="group" required :options="$site['group_types']" placeholder="Choose one" />
                        <x-field name="group_size" type="number" label="How many people?" bag="group" required min="2" max="300" inputmode="numeric" />
                        <x-field name="tour_date" type="date" label="Preferred date" bag="group" :min="now(config('site.timezone'))->addDay()->toDateString()" />
                        <div class="flex items-end pb-2.5">
                            <label class="flex items-center gap-2.5 text-sm">
                                <input type="hidden" name="date_flexible" value="0">
                                <input type="checkbox" name="date_flexible" value="1" class="size-4 rounded accent-brand-600" @checked($oldGroup('date_flexible'))> Our date is flexible
                            </label>
                        </div>

                        <fieldset class="sm:col-span-2">
                            <legend class="label">What would you like to see? <span class="text-brand-600" aria-hidden="true">*</span></legend>
                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach ($site['interests'] as $interest)
                                    <label class="option">
                                        <input type="checkbox" name="interests[]" value="{{ $interest }}" class="peer sr-only" @checked(in_array($interest, (array) $oldGroup('interests', [])))>
                                        <span><x-icon name="check" class="size-4 shrink-0 text-brand-600 opacity-0 transition [input:checked+span>&]:opacity-100" /> {{ $interest }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if ($groupBag->has('interests'))
                                <p class="field-error">{{ $groupBag->first('interests') }}</p>
                            @endif
                        </fieldset>

                        <x-field name="message" type="textarea" label="Tell us more" bag="group" class="sm:col-span-2" rows="4"
                            placeholder="Ages, ship and docking times, anything you need us to plan around" />
                        <x-field name="name" label="Your name" bag="group" required autocomplete="name" />
                        <x-field name="phone" type="tel" label="Phone or WhatsApp" bag="group" required autocomplete="tel" placeholder="With country code" />
                        <x-field name="email" type="email" label="Email" bag="group" required autocomplete="email" class="sm:col-span-2" />
                    </div>

                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-5">Send group request <x-icon name="send" class="size-4" /></button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
