# Configuration Guide

## Environment Variables

Add the following environment variables to your `.env` file:

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Cache Configuration
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### News API Keys
```env
# Get your API keys from the respective services
NEWSAPI_KEY=your_newsapi_key_here
GUARDIAN_KEY=your_guardian_key_here
NYT_KEY=your_nyt_key_here
BBC_KEY=your_bbc_key_here
```

### Provider Settings
```env
# Enable/disable specific news providers
NEWSAPI_ENABLED=true
GUARDIAN_ENABLED=true
NYT_ENABLED=true
BBC_ENABLED=true
```

### News Defaults
```env
NEWS_DEFAULT_LANGUAGE=en
NEWS_DEFAULT_PAGE_SIZE=50
```

## API Key Setup

### 1. NewsAPI.org
- Visit: https://newsapi.org/
- Sign up for a free account
- Get your API key from the dashboard
- Add to `NEWSAPI_KEY` in your `.env` file

### 2. The Guardian
- Visit: https://open-platform.theguardian.com/
- Register for an API key
- Add to `GUARDIAN_KEY` in your `.env` file

### 3. New York Times
- Visit: https://developer.nytimes.com/
- Sign up for an API key
- Add to `NYT_KEY` in your `.env` file

### 4. BBC News
- Uses NewsAPI.org with BBC as the source
- Use the same `NEWSAPI_KEY` for `BBC_KEY`

## Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE news_aggregator;
   ```

2. Run migrations:
   ```bash
   php artisan migrate
   ```

3. (Optional) Seed with sample data:
   ```bash
   php artisan db:seed
   ```

## Redis Setup

### Ubuntu/Debian
```bash
sudo apt update
sudo apt install redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### macOS
```bash
brew install redis
brew services start redis
```

### Windows
Download and install Redis from: https://github.com/microsoftarchive/redis/releases

## Testing the Setup

1. Test database connection:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

2. Test Redis connection:
   ```bash
   php artisan tinker
   Cache::put('test', 'value');
   Cache::get('test');
   ```

3. Test news fetching:
   ```bash
   php artisan news:fetch
   ```

4. Test API endpoints:
   ```bash
   curl http://localhost:8000/api/articles
   ```

## Production Considerations

1. **Security**:
   - Set `APP_DEBUG=false`
   - Use strong database passwords
   - Enable HTTPS
   - Set up proper firewall rules

2. **Performance**:
   - Use Redis for caching
   - Enable query caching
   - Set up proper database indexing
   - Use a CDN for static assets

3. **Monitoring**:
   - Set up log monitoring
   - Monitor API rate limits
   - Set up health checks
   - Monitor database performance

4. **Scaling**:
   - Use load balancers
   - Set up database replication
   - Use Redis clustering
   - Implement queue workers
