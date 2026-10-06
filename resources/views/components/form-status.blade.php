@props(['type', 'title' => 'Thank you, we have it.', 'message' => 'A member of our team will be in touch shortly.'])
@php($success = session('enquiry_success'))
@if ($success && $success['type'] === $type)
    <div class="mb-6 flex gap-4 rounded-2xl bg-emerald-50 p-5 text-emerald-950 ring-1 ring-emerald-200" role="status">
        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
            <x-icon name="check" class="size-5" />
        </span>
        <div>
            <p class="font-display text-2xl font-semibold">{{ $title }}</p>
            <p class="mt-0.5 text-sm text-emerald-900/80">
                {{ $message }}
                @if ($success['reference'] ?? null)
                    Your reference is <strong class="font-semibold">{{ $success['reference'] }}</strong>.
                @endif
            </p>
        </div>
    </div>
@elseif ($errors->getBag($type)->any())
    <div class="mb-6 flex gap-3 rounded-2xl bg-rose-50 p-4 text-sm text-rose-900 ring-1 ring-rose-200" role="alert">
        <x-icon name="triangle-alert" class="mt-0.5 size-5 shrink-0 text-rose-600" />
        <p><strong class="font-semibold">Please check the form.</strong> {{ $errors->getBag($type)->count() }} {{ Str::plural('field', $errors->getBag($type)->count()) }} need your attention.</p>
    </div>
@endif
