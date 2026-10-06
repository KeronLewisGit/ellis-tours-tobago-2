@php
    $site = config('site');
    $whatsapp = \App\Support\Content::whatsapp('Hi Ellis, I got your WhatsApp information from your website.');
@endphp
<x-layout title="Contact" description="Contact Ellis Tours Tobago by WhatsApp, phone or email, or find us at Pigeon Point Beach Facility, Crown Point.">
    <x-page-hero eyebrow="Contact" title="Get in touch."
        lead="For enquiries and further information, send a WhatsApp message or fill out the form. To reserve a tour, use the booking page.">
        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><x-icon name="message-circle" class="size-4" /> Chat on WhatsApp</a>
        <a href="{{ route('book') }}" class="btn btn-onDark">Book a tour <x-icon name="arrow-right" class="size-4" /></a>
    </x-page-hero>

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5" data-reveal>
                <dl class="space-y-5 text-sm">
                    @foreach ([
                        ['message-circle', 'WhatsApp', $site['phone'], $whatsapp],
                        ['phone', 'Call', $site['phone'], 'tel:'.$site['phone_href']],
                        ['mail', 'Email', $site['email'], 'mailto:'.$site['email']],
                        ['map-pin', 'Visit us', $site['address'], $site['links']['maps']],
                    ] as [$icon, $label, $value, $url])
                        <div class="flex items-center gap-4">
                            <span class="icon-tile"><x-icon :name="$icon" /></span>
                            <div class="min-w-0">
                                <dt class="text-muted">{{ $label }}</dt>
                                <dd class="text-base font-semibold break-words"><a href="{{ $url }}" @if (str_starts_with($url, 'https://')) target="_blank" rel="noopener" @endif class="transition hover:text-brand-700">{{ $value }}</a></dd>
                            </div>
                        </div>
                    @endforeach
                </dl>

                <a href="{{ $site['links']['maps'] }}" target="_blank" rel="noopener" class="link-arrow mt-6">Directions and maps <x-icon name="arrow-up-right" class="size-4" /></a>

                <div class="mt-10 border-t border-line pt-8">
                    <h2 class="font-sans text-xs font-semibold tracking-[0.16em] text-muted uppercase">Find us on socials</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'tripadvisor' => 'Tripadvisor'] as $key => $name)
                            <a href="{{ $site['links'][$key] }}" target="_blank" rel="noopener" class="btn btn-light !py-2.5">{{ $name }} <x-icon name="arrow-up-right" class="size-4" /></a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7" data-reveal style="--reveal-delay: 120ms">
                <div class="card">
                    <h2 class="font-display text-2xl font-semibold">Send a message</h2>
                    <p class="mt-1 mb-6 text-sm text-muted">Ellis is often out on tour, so WhatsApp is quickest. Messages sent here are answered as soon as he is back.</p>
                    <x-contact-form />
                </div>
            </div>
        </div>
    </section>
</x-layout>
