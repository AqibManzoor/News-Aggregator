# 🚀 Deployment Guide

## Production Deployment

### 1. Server Requirements
- PHP 8.0.2 or higher
- MySQL 5.7+ or PostgreSQL 10+
- Composer
- Git
- Web server (Apache/Nginx)

### 2. Quick Deployment

```bash
# Clone the repository
git clone <your-github-url>
cd news-aggregator

# Install dependencies
composer install --optimize-autoloader --no-dev

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate --force

# (Optional) Trigger initial fetch from providers
php artisan news:fetch

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Start the application
php artisan serve --host=0.0.0.0 --port=8000
```

 

### 4. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/news-aggregator/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/news-aggregator/public
    
    <Directory /path/to/news-aggregator/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 6. Scheduled Tasks

Add to crontab for automated news fetching:

```bash
# Edit crontab
crontab -e

# Add this line
* * * * * cd /path/to/news-aggregator && php artisan schedule:run >> /dev/null 2>&1
```

### 7. Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=news_aggregator
DB_USERNAME=your_username
DB_PASSWORD=your_password

# News API Keys (get your own)
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_KEY=your_guardian_key
NYT_KEY=your_nyt_key
BBC_KEY=your_bbc_key
```

### 8. Security Considerations

- Set `APP_DEBUG=false` in production
- Use strong database passwords
- Enable HTTPS
- Set proper file permissions
- Use environment variables for sensitive data
- Regular security updates

### 9. Monitoring

- Monitor application logs: `storage/logs/laravel.log`
- Set up health checks
- Monitor database performance
- Track API usage

### 10. Backup

```bash
# Database backup
mysqldump -u username -p news_aggregator > backup.sql

# Application backup
tar -czf news-aggregator-backup.tar.gz /path/to/news-aggregator
```

---

**Your News Aggregator is now production-ready! 🎉**
