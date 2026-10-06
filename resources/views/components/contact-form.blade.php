@props(['topic' => null, 'compact' => false])
@php($bag = 'contact')
<form id="form-contact" method="POST" action="{{ route('enquiries.store', 'contact') }}" {{ $attributes->merge(['class' => 'scroll-mt-28']) }}>
    @csrf
    <x-form-status type="contact" title="Message sent." message="Thanks for getting in touch. Ellis will reply as soon as he is back from tour." />

    <div class="grid gap-4 sm:grid-cols-2">
        <x-field name="name" label="Name" :bag="$bag" required autocomplete="name" />
        <x-field name="email" type="email" label="Email" :bag="$bag" required autocomplete="email" />
        @unless ($compact)
            <x-field name="phone" type="tel" label="Phone or WhatsApp" :bag="$bag" autocomplete="tel" placeholder="Optional, with country code" />
        @endunless
        <x-field name="topic" type="select" label="What can we help with?" :bag="$bag"
            :options="config('site.topics')" :value="$topic ?? 'General enquiry'"
            @class(['sm:col-span-2' => $compact]) />
        <x-field name="message" type="textarea" label="Message" :bag="$bag" required class="sm:col-span-2" rows="5" />
    </div>

    {{-- Honeypot --}}
    <div class="hidden" aria-hidden="true">
        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="mt-5 flex flex-wrap items-center gap-4">
        <button type="submit" class="btn btn-primary">
            Send message <x-icon name="arrow-right" class="size-4" />
        </button>
        <p class="text-xs text-muted">Or WhatsApp <a class="font-semibold text-ink" href="{{ \App\Support\Content::whatsapp() }}" target="_blank" rel="noopener">{{ config('site.phone') }}</a></p>
    </div>
</form>
