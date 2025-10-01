# News Aggregator API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
No authentication required for this API.

## Response Format
All responses follow a consistent JSON format:

### Success Response
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200,
    "from": 1,
    "to": 20
  },
  "links": {
    "first": "http://localhost:8000/api/articles?page=1",
    "last": "http://localhost:8000/api/articles?page=10",
    "prev": null,
    "next": "http://localhost:8000/api/articles?page=2"
  }
}
```

### Error Response
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["The field name field is required."]
  }
}
```

## Endpoints

### Articles

#### GET /articles
Retrieve a list of articles with optional filtering and pagination.

**Query Parameters:**
| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `q` | string | Search query | `?q=technology` |
| `source` | string | Filter by source name | `?source=BBC News` |
| `sources` | array | Filter by multiple sources | `?sources[]=BBC News&sources[]=CNN` |
| `category` | string | Filter by category | `?category=technology` |
| `categories` | array | Filter by multiple categories | `?categories[]=technology&categories[]=science` |
| `author` | string | Filter by author name | `?author=John Doe` |
| `authors` | array | Filter by multiple authors | `?authors[]=John Doe&authors[]=Jane Smith` |
| `from` | date | Filter from date (Y-m-d) | `?from=2024-01-01` |
| `to` | date | Filter to date (Y-m-d) | `?to=2024-01-31` |
| `language` | string | Filter by language | `?language=en` |
| `sort` | string | Sort order (newest, oldest, title) | `?sort=newest` |
| `per_page` | integer | Items per page (1-100) | `?per_page=20` |
| `page` | integer | Page number | `?page=2` |

**Example Request:**
```bash
curl "http://localhost:8000/api/articles?q=technology&source=BBC News&sort=newest&per_page=10"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Latest Technology News",
      "slug": "latest-technology-news",
      "summary": "A comprehensive overview of the latest technology developments...",
      "url": "https://example.com/article",
      "image_url": "https://example.com/image.jpg",
      "published_at": "2024-01-15T10:30:00.000000Z",
      "language": "en",
      "source": {
        "id": 1,
        "name": "BBC News",
        "slug": "bbc-news"
      },
      "categories": [
        {
          "id": 1,
          "name": "Technology",
          "slug": "technology"
        }
      ],
      "authors": [
        {
          "id": 1,
          "name": "John Doe"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 50,
    "from": 1,
    "to": 10
  },
  "links": {
    "first": "http://localhost:8000/api/articles?page=1",
    "last": "http://localhost:8000/api/articles?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/articles?page=2"
  }
}
```

#### GET /articles/{id}
Retrieve a specific article by ID.

**Example Request:**
```bash
curl "http://localhost:8000/api/articles/1"
```

**Example Response:**
```json
{
  "data": {
    "id": 1,
    "title": "Latest Technology News",
    "slug": "latest-technology-news",
    "summary": "A comprehensive overview of the latest technology developments...",
    "content": "Full article content here...",
    "url": "https://example.com/article",
    "image_url": "https://example.com/image.jpg",
    "published_at": "2024-01-15T10:30:00.000000Z",
    "language": "en",
    "source": {
      "id": 1,
      "name": "BBC News",
      "slug": "bbc-news"
    },
    "categories": [
      {
        "id": 1,
        "name": "Technology",
        "slug": "technology"
      }
    ],
    "authors": [
      {
        "id": 1,
        "name": "John Doe"
      }
    ]
  }
}
```

#### GET /articles/stats
Get article statistics.

**Example Request:**
```bash
curl "http://localhost:8000/api/articles/stats"
```

**Example Response:**
```json
{
  "data": {
    "total_articles": 1250,
    "articles_today": 45,
    "articles_this_week": 320,
    "sources_count": 4,
    "categories_count": 12,
    "authors_count": 156
  }
}
```

### Sources

#### GET /sources
Retrieve a list of all news sources.

**Example Request:**
```bash
curl "http://localhost:8000/api/sources"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "BBC News",
      "slug": "bbc-news",
      "external_id": "bbc-news",
      "website_url": "https://www.bbc.com/news",
      "articles_count": 250
    },
    {
      "id": 2,
      "name": "The Guardian",
      "slug": "the-guardian",
      "external_id": "guardian",
      "website_url": "https://www.theguardian.com",
      "articles_count": 180
    }
  ]
}
```

### Categories

#### GET /categories
Retrieve a list of all article categories.

**Example Request:**
```bash
curl "http://localhost:8000/api/categories"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Technology",
      "slug": "technology",
      "articles_count": 150
    },
    {
      "id": 2,
      "name": "Science",
      "slug": "science",
      "articles_count": 120
    }
  ]
}
```

### Authors

#### GET /authors
Retrieve a list of all authors.

**Example Request:**
```bash
curl "http://localhost:8000/api/authors"
```

**Example Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "articles_count": 25
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "articles_count": 18
    }
  ]
}
```

### Fetch

#### POST /fetch
Manually trigger news fetching from all enabled providers.

**Request Body:**
```json
{
  "q": "technology",
  "category": "tech",
  "from": "2024-01-01",
  "to": "2024-01-31",
  "language": "en",
  "page": 1,
  "pageSize": 50
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/api/fetch" \
  -H "Content-Type: application/json" \
  -d '{"q": "technology", "pageSize": 100}'
```

**Example Response:**
```json
{
  "status": "ok",
  "fetched": 150,
  "inserted": 120,
  "updated": 30
}
```

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request |
| 422 | Validation Error |
| 500 | Internal Server Error |

## Rate Limiting

The API implements rate limiting to prevent abuse. Default limits:
- 1000 requests per hour per IP
- 100 requests per minute per IP

## Caching

The API uses Redis for caching to improve performance:
- Article lists are cached for 1 hour
- Statistics are cached for 30 minutes
- Sources, categories, and authors are cached for 1 hour

## Examples

### Search for Technology Articles
```bash
curl "http://localhost:8000/api/articles?q=technology&sort=newest&per_page=20"
```

### Filter by Date Range
```bash
curl "http://localhost:8000/api/articles?from=2024-01-01&to=2024-01-31"
```

### Filter by Multiple Sources
```bash
curl "http://localhost:8000/api/articles?sources[]=BBC News&sources[]=The Guardian"
```

### Get Articles by Author
```bash
curl "http://localhost:8000/api/articles?author=John Doe"
```

### Pagination
```bash
curl "http://localhost:8000/api/articles?page=2&per_page=10"
```

## SDK Examples

### JavaScript/Node.js
```javascript
const axios = require('axios');

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
});

// Get articles
const articles = await api.get('/articles', {
  params: {
    q: 'technology',
    sort: 'newest',
    per_page: 20
  }
});

// Get statistics
const stats = await api.get('/articles/stats');
```

### PHP
```php
$client = new GuzzleHttp\Client(['base_uri' => 'http://localhost:8000/api']);

// Get articles
$response = $client->get('/articles', [
    'query' => [
        'q' => 'technology',
        'sort' => 'newest',
        'per_page' => 20
    ]
]);

$articles = json_decode($response->getBody(), true);
```

### Python
```python
import requests

base_url = 'http://localhost:8000/api'

# Get articles
response = requests.get(f'{base_url}/articles', params={
    'q': 'technology',
    'sort': 'newest',
    'per_page': 20
})

articles = response.json()
```
