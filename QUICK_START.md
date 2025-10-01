# 🚀 Quick Start Guide

## Manual Setup

```bash
# 1. Clone the repository
git clone <your-github-url>
cd news-aggregator

# 2. Run the setup script
chmod +x setup.sh
./setup.sh

# 3. Start the server
php artisan serve

# 4. Access the application
# Web Interface: http://localhost:8000
# API: http://localhost:8000/api/articles
```

## 🎯 What You Get

- **Professional Web Interface** with advanced filtering
- **REST API** with comprehensive endpoints
- **Sample Data** pre-loaded for testing
- **Automated News Fetching** every hour
- **Zero Configuration** - works out of the box

## 📊 Test the API

```bash
# Get all articles
curl "http://localhost:8000/api/articles"

# Search for technology articles
curl "http://localhost:8000/api/articles?q=technology"

# Filter by source
curl "http://localhost:8000/api/articles?source=BBC News"

# Get paginated results
curl "http://localhost:8000/api/articles?page=1&per_page=5"
```

## 🔧 Configuration

The project comes with pre-configured API keys for testing. For production:

1. Edit `.env` file
2. Replace API keys with your own
3. Update database credentials if needed

## 📖 Full Documentation

See `README.md` for complete documentation and advanced configuration options.

---

**That's it! Your News Aggregator is ready to use! 🎉**
