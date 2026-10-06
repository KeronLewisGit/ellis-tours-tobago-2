@php
    $site = config('site');
@endphp
<x-layout title="Questions and answers" description="How to pay, where pickup happens, what to wear, whether lunch is included and how cruise ship and group tours work with Ellis Tours Tobago.">
    <x-page-hero eyebrow="Before you book" title="Questions and answers"
        lead="The things guests ask most often. If yours is not here, send Ellis a WhatsApp message." />

    <section class="section">
        <div class="wrap grid gap-12 lg:grid-cols-12">
            <div class="space-y-3 lg:col-span-8">
                @foreach ($site['faqs'] as [$question, $answer])
                    <details id="{{ Str::slug($question) }}" class="group scroll-mt-28 rounded-2xl bg-white shadow-card ring-1 ring-line/70 open:ring-brand-300" data-reveal @if ($loop->first) open @endif>
                        <summary class="flex list-none items-center justify-between gap-4 p-5 font-display text-lg font-semibold sm:p-6 [&::-webkit-details-marker]:hidden">
                            {{ $question }}
                            <x-icon name="chevron-down" class="size-5 shrink-0 text-brand-600 transition group-open:rotate-180" />
                        </summary>
                        <p class="px-5 pb-5 leading-relaxed text-ink/80 sm:px-6 sm:pb-6">{{ $answer }}</p>
                    </details>
                @endforeach
            </div>

            <aside class="space-y-5 lg:col-span-4">
                <div class="rounded-3xl bg-ink p-6 text-white sm:p-8" data-reveal>
                    <p class="text-xs font-semibold tracking-[0.16em] text-white/50 uppercase">Still wondering?</p>
                    <p class="mt-3 font-display text-2xl font-semibold">Ask Ellis directly.</p>
                    <div class="mt-5 space-y-2">
                        <a href="{{ \App\Support\Content::whatsapp('Hi Ellis, I have a question about your tours.') }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><x-icon name="message-circle" class="size-4" /> WhatsApp {{ $site['phone'] }}</a>
                        <a href="{{ route('contact') }}" class="btn btn-onDark w-full"><x-icon name="mail" class="size-4" /> Send a message</a>
                    </div>
                </div>
                <div class="card !p-6" data-reveal style="--reveal-delay: 100ms">
                    <h2 class="h-card">Ready to go?</h2>
                    <p class="mt-2 text-sm text-muted">Check your date and book in three short steps.</p>
                    <a href="{{ route('book') }}" class="btn btn-primary mt-4 w-full">Book a tour <x-icon name="arrow-right" class="size-4" /></a>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
