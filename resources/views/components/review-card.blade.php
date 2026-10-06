@props(['review'])
<figure {{ $attributes->merge(['class' => 'card flex flex-col']) }}>
    @if ($review->rating)
        <p class="flex gap-0.5" role="img" aria-label="{{ $review->rating }} out of 5 stars">
            @for ($star = 1; $star <= 5; $star++)
                <x-icon name="star" class="size-4 {{ $star <= $review->rating ? 'fill-brand-400 text-brand-400' : 'text-line' }}" />
            @endfor
        </p>
    @else
        <x-icon name="quote" class="size-7 text-brand-400" />
    @endif
    <blockquote class="mt-4 flex-1 text-[15px] leading-relaxed text-ink/85">&ldquo;{{ $review->body }}&rdquo;</blockquote>
    <figcaption class="mt-5 border-t border-line pt-4 text-sm">
        <p class="font-semibold">{{ $review->name }}@if ($review->origin)<span class="font-normal text-muted">, {{ $review->origin }}</span>@endif</p>
        <p class="mt-0.5 text-xs text-muted">
            @if ($review->tour_title){{ $review->tour_title }} &middot; @endif
            @if ($review->source_url)
                <a href="{{ $review->source_url }}" target="_blank" rel="noopener" class="underline decoration-line underline-offset-2 hover:text-ink">{{ $review->sourceLabel() }}</a>
            @else
                {{ $review->sourceLabel() }}
            @endif
        </p>
    </figcaption>
</figure>
