<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Copies every Lucide icon referenced in the views/config from node_modules into
| resources/icons, so the app does not depend on node_modules at runtime.
*/
Artisan::command('icons:sync', function () {
    $sources = [...File::allFiles(resource_path('views')), ...File::allFiles(app_path()), ...File::allFiles(config_path())];
    $names = collect($sources)
        ->flatMap(function ($file) {
            preg_match_all('/[\'"]([a-z0-9]+(?:-[a-z0-9]+)*)[\'"]/', $file->getContents(), $matches);

            return $matches[1];
        })
        ->unique();

    $copied = $names->filter(function (string $name) {
        $source = base_path("node_modules/lucide-static/icons/{$name}.svg");

        return is_file($source) && File::copy($source, resource_path("icons/{$name}.svg"));
    });

    $this->info("Synced {$copied->count()} icons.");
})->purpose('Copy the Lucide icons used by the site into resources/icons');
