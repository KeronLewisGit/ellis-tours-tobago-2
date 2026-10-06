#!/bin/zsh
#
# Double-click this file (or run ./preview.command) to preview the Ellis Tours Tobago site.
# It serves the site on this Mac and to other devices on the same Wi-Fi,
# then opens it in your browser. Close the window or press Ctrl+C to stop.
#
# Pass --private to serve on this Mac only:  ./preview.command --private

cd "$(dirname "$0")" || exit 1

PORT=8020
HOST=0.0.0.0
[[ "$1" == "--private" ]] && HOST=127.0.0.1

# First-run setup, so the preview also works from a fresh copy of the project.
[[ -d vendor ]] || composer install --no-interaction || exit 1
[[ -f .env ]] || { cp .env.example .env && php artisan key:generate --force; }
[[ -f database/database.sqlite ]] || { touch database/database.sqlite && php artisan migrate --seed --force; }
[[ -f public/build/manifest.json ]] || { npm install && npm run build; } || exit 1

# Replace a preview that is already running; never touch another app's port.
for pid in $(lsof -ti tcp:$PORT -sTCP:LISTEN 2>/dev/null); do
    if [[ "$(ps -p $pid -o comm=)" == *php* ]]; then
        kill $pid && sleep 1
    else
        echo "Port $PORT is in use by another app ($(ps -p $pid -o comm=)). Close it and try again."
        exit 1
    fi
done

IP=$(ipconfig getifaddr en0 2>/dev/null || ipconfig getifaddr en1 2>/dev/null)
NAME=$(scutil --get LocalHostName 2>/dev/null)

echo
echo "  Ellis Tours Tobago preview is running"
echo
echo "  On this Mac        http://localhost:$PORT"
if [[ "$HOST" == "0.0.0.0" && -n "$IP" ]]; then
    echo "  Phone or tablet    http://$IP:$PORT   (same Wi-Fi)"
    [[ -n "$NAME" ]] && echo "  Apple devices      http://$NAME.local:$PORT"
fi
echo "  Staff admin        http://localhost:$PORT/admin/login"
echo
echo "  Press Ctrl+C to stop."
echo

[[ -z "$ETT_NO_OPEN" ]] && (sleep 1 && open "http://localhost:$PORT") &

# Same server "php artisan serve" starts, with room for larger form uploads.
cd public && exec php -d upload_max_filesize=16M -d post_max_size=20M \
    -S "$HOST:$PORT" ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
