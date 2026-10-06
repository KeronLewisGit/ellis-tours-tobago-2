<?php

namespace App\Support;

class Content
{
    protected static array $icons = [];

    /** Inline a Lucide icon from resources/icons. */
    public static function icon(string $name, string $class = 'size-5', string $attributes = ''): string
    {
        if (! isset(self::$icons[$name])) {
            $path = resource_path("icons/{$name}.svg");
            $svg = is_file($path) ? file_get_contents($path) : '<svg viewBox="0 0 24 24"></svg>';
            $svg = preg_replace('/<!--.*?-->/s', '', $svg);
            // Drop the sizing attributes from the root <svg> only, so shapes such as <rect> keep theirs.
            $svg = preg_replace_callback('/<svg\b[^>]*>/', fn (array $tag) => preg_replace('/\s(class|width|height)="[^"]*"/', '', $tag[0]), $svg, 1);
            self::$icons[$name] = trim(preg_replace('/\s+/', ' ', $svg));
        }

        return str_replace('<svg', '<svg class="'.e($class).'" aria-hidden="true" '.$attributes, self::$icons[$name]);
    }

    /** A wa.me link to the Ellis's WhatsApp, optionally with a pre-filled message. */
    public static function whatsapp(?string $message = null): string
    {
        return 'https://wa.me/'.config('site.whatsapp').($message ? '?text='.rawurlencode($message) : '');
    }
}
