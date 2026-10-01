#!/bin/sh
set -eu

: "${APP_KEY:?Set APP_KEY in Render environment variables}"

# Accept the conventional name used by some Render database integrations.
if [ -z "${DB_URL:-}" ] && [ -n "${DATABASE_URL:-}" ]; then
    export DB_URL="$DATABASE_URL"
fi

: "${DB_URL:?Set DB_URL to the Render PostgreSQL connection string}"

echo "Starting GradeFlow..."

port="${PORT:-10000}"
case "$port" in
    ''|*[!0-9]*) echo "PORT must be a number" >&2; exit 1 ;;
esac

sed -i "s/Listen 80/Listen $port/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$port>/" /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan migrate --force
php artisan db:seed --class=SubjectSeeder --force
php artisan gradeflow:bootstrap-admin
php artisan db:seed --class=StudentSeeder --force

exec apache2-foreground
