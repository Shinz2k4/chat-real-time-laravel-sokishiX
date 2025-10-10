#!/bin/bash

# Laravel Performance Optimization Script
echo "🚀 Optimizing Laravel Performance..."

# Set production environment
export APP_ENV=production
export APP_DEBUG=false

# Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate optimized caches
echo "⚡ Generating optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
echo "📦 Optimizing Composer autoloader..."
composer dump-autoload --optimize --no-dev

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create optimized PHP configuration
echo "⚙️ Creating PHP optimization config..."
cat > /usr/local/etc/php/conf.d/99-performance.ini << EOF
; Performance optimizations
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.fast_shutdown=1
opcache.enable_file_override=1

; Realpath cache
realpath_cache_size=4096K
realpath_cache_ttl=600

; Memory limits
memory_limit=256M
max_execution_time=30
max_input_time=30

; Upload limits
upload_max_filesize=32M
post_max_size=32M
max_file_uploads=20
EOF

echo "✅ Laravel optimization complete!"
echo "📊 Performance improvements applied:"
echo "   - OPcache enabled with 256MB memory"
echo "   - Config/Route/View caching enabled"
echo "   - Composer autoloader optimized"
echo "   - Realpath cache configured"
echo "   - Memory limits optimized"
