#!/usr/bin/env bash
# After the Cloudflare Tunnel hostname points at 127.0.0.1:8081, switch the app
# to its public https address:
#   bash /opt/webstack/set-public-url-rezervuj.sh https://rezervuj-ma.online
set -euo pipefail
URL="${1:?usage: set-public-url-rezervuj.sh https://your-domain}"
ENV=/opt/webstack/rezervuj/.env
sed -i "s|^APP_URL=.*|APP_URL=${URL}|" "$ENV"
if grep -q "^SESSION_SECURE_COOKIE" "$ENV"; then sed -i "s|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=true|" "$ENV"; else echo "SESSION_SECURE_COOKIE=true" >> "$ENV"; fi
podman exec -u 33 web-rezervuj php artisan config:cache >/dev/null
podman exec -u 33 web-rezervuj php artisan route:cache >/dev/null
systemctl restart rezervuj
echo "APP_URL=${URL} set, caches rebuilt, container restarted."
