@props(['name'])
{!! \App\Support\Content::icon($name, $attributes->get('class', 'size-5'), (string) $attributes->except('class')) !!}
