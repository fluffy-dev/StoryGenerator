#!/bin/bash
set -e

echo "🔧 Setting permissions..."
chmod -R 777 runtime web/assets

if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --prefer-dist --no-progress --no-interaction
fi

echo "⏳ Waiting for Database to be ready..."
max_tries=10
counter=0

until php yii migrate --interactive=0; do
    counter=$((counter+1))
    if [ $counter -ge $max_tries ]; then
        echo "❌ Database connection failed after $max_tries attempts."
        exit 1
    fi
    echo "zzz Database not ready yet, retrying in 5 seconds... ($counter/$max_tries)"
    sleep 5
done

echo "✅ Migrations applied successfully."

echo "🚀 Starting Apache..."
exec apache2-foreground