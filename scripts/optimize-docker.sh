#!/bin/bash

# Docker Performance Optimization Script
echo "🚀 Optimizing Docker Performance for SokishiX Chat..."

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker compose down

# Remove old images to force rebuild
echo "🗑️ Removing old images..."
docker image rm laravel-chat-app-php 2>/dev/null || true

# Build with no cache for fresh optimization
echo "🔨 Building optimized containers..."
docker compose build --no-cache app

# Start services
echo "▶️ Starting optimized services..."
docker compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Run Laravel optimizations
echo "⚡ Running Laravel optimizations..."
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache
docker compose exec app composer dump-autoload --optimize

# Set permissions
echo "🔐 Setting proper permissions..."
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# Check OPcache status
echo "📊 Checking OPcache status..."
docker compose exec app php -r "if (function_exists('opcache_get_status')) { \$status = opcache_get_status(); echo 'OPcache enabled: ' . (\$status['opcache_enabled'] ? 'YES' : 'NO') . PHP_EOL; echo 'Memory used: ' . round(\$status['memory_usage']['used_memory'] / 1024 / 1024, 2) . 'MB' . PHP_EOL; } else { echo 'OPcache not available' . PHP_EOL; }"

# Show container status
echo "📋 Container status:"
docker compose ps

echo "✅ Performance optimization complete!"
echo ""
echo "🎯 Performance improvements applied:"
echo "   ✅ OPcache enabled with 256MB memory"
echo "   ✅ Redis caching enabled"
echo "   ✅ Optimized Docker volumes (delegated)"
echo "   ✅ Nginx gzip compression"
echo "   ✅ Static file caching (1 year)"
echo "   ✅ Laravel config/route/view caching"
echo "   ✅ Composer autoloader optimized"
echo "   ✅ PHP-FPM process management optimized"
echo "   ✅ Memory limits configured"
echo ""
echo "🌐 Access your optimized app at: http://localhost"
