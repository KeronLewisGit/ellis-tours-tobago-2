@props([
    'name',
    'label' => null,
    'type' => 'text',
    'bag' => 'default',
    'required' => false,
    'hint' => null,
    'options' => [],
    'value' => null,
    'placeholder' => null,
])
@php
    $id = $bag.'-'.$name;
    $errorBag = $errors->getBag($bag);
    $error = $errorBag->first($name);
    // Only repopulate the form that was actually submitted.
    $current = $errorBag->any() ? old($name, $value) : $value;
    $control = $attributes->except('class')->merge([
        'id' => $id,
        'name' => $name,
        'class' => 'input'.($error ? ' input-error' : ''),
        'aria-invalid' => $error ? 'true' : null,
        'aria-describedby' => $error ? $id.'-error' : null,
    ]);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="label">
            {{ $label }}
            @if ($required)
                <span class="text-brand-600" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea {{ $control->merge(['rows' => 4]) }} placeholder="{{ $placeholder }}" @required($required)>{{ $current }}</textarea>
    @elseif ($type === 'select')
        <select {{ $control }} @required($required)>
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $key => $text)
                @php($optionValue = is_int($key) ? $text : $key)
                <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $text }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" {{ $control }} value="{{ $current }}" placeholder="{{ $placeholder }}" @required($required)>
    @endif

    @if ($error)
        <p id="{{ $id }}-error" class="field-error">{{ $error }}</p>
    @elseif ($hint)
        <p class="hint">{{ $hint }}</p>
    @endif
</div>
