#!/usr/bin/env bash
# Server-side deploy for rezervuj-ma.online on the box.
# Called by deploy.ps1 after a new code tarball has been extracted to
# /opt/webstack/rezervuj, or run manually after `git pull` there.
#   Usage: bash /opt/webstack/deploy-rezervuj.sh
set -euo pipefail

APP=/opt/webstack/rezervuj
CLI_IMAGE=docker.io/serversideup/php:8.4-cli
CONTAINER=web-rezervuj
SERVICE=rezervuj
PORT=8081

[ -f "$APP/.env" ] || { echo "no .env in $APP – run deploy/bootstrap-rezervuj.sh first"; exit 1; }

chown -R 33:33 "$APP"
chmod -R ug+rwX "$APP/storage" "$APP/bootstrap/cache"
echo ">> composer install (no-dev, optimized)"
podman run --rm --network host -v "$APP":/var/www/html:Z -w /var/www/html \
  "$CLI_IMAGE" composer install --no-dev --optimize-autoloader --no-interaction
chown -R 33:33 "$APP"

# keep the host copies of the unit files in sync with the repo
install -m 644 "$APP/deploy/rezervuj.container" /etc/containers/systemd/rezervuj.container
install -m 644 "$APP/deploy/rezervuj-schedule.service" /etc/systemd/system/rezervuj-schedule.service
install -m 644 "$APP/deploy/rezervuj-schedule.timer" /etc/systemd/system/rezervuj-schedule.timer
systemctl daemon-reload

echo ">> database migrations"
podman exec -u 33 "$CONTAINER" php artisan migrate --force --no-interaction

echo ">> clear + rebuild caches"
podman exec -u 33 "$CONTAINER" php artisan optimize:clear
podman exec -u 33 "$CONTAINER" php artisan config:cache
podman exec -u 33 "$CONTAINER" php artisan route:cache
podman exec -u 33 "$CONTAINER" php artisan view:cache
podman exec -u 33 "$CONTAINER" php artisan storage:link --force >/dev/null 2>&1 || true

echo ">> restart web container"
systemctl restart "$SERVICE"
systemctl is-active --quiet rezervuj-schedule.timer || systemctl enable --now rezervuj-schedule.timer

sleep 6
code=$(curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:${PORT}/" || echo 000)
echo ">> health check: HTTP $code"
if [ "$code" = "200" ] || [ "$code" = "302" ]; then echo "✅ DEPLOY OK"; else echo "⚠️ unexpected status $code — check: podman logs $CONTAINER"; exit 1; fi
