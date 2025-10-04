# Siraj Al-Din API Documentation

## Overview
This API provides access to the Siraj Al-Din Islamic Content Management System database. The API is read-only and provides structured access to various content types including articles, books, videos, sounds, photo galleries, and more.

## Base URL
```
http://localhost:8000/api
```

## Working Endpoints Status

### ✅ Working Endpoints
- **Health Check**: `/health` - API status check
- **Test**: `/test` - Basic functionality test
- **Simple Sections**: `/sections-simple` - Main sections (menus) only
- **Menus**: All menu-related endpoints
- **Articles**: All article-related endpoints
- **Books**: All book-related endpoints
- **Videos**: All video-related endpoints
- **Sounds**: All sound-related endpoints
- **Pages**: All page-related endpoints
- **Blocks**: All block-related endpoints
- **Links**: All link-related endpoints

### ⚠️ Endpoints with Issues
- **Hierarchy**: `/hierarchy` - Menu-Page relationship (500 error)
- **Photo Galleries**: `/photo-galleries` - Photo gallery endpoints (500 error)
- **Dashboard Summary**: `/dashboard/summary` - Summary statistics (500 error)

## Main Sections (Hierarchy)

### Get Simple Sections
```http
GET /api/sections-simple
```

**Response:**
```json
{
  "success": true,
  "data": {
    "sections": [
      {
        "id": 46,
        "name": "السيرة الذاتية",
        "url": "#",
        "priority": 20,
        "language": "ar",
        "date": "2014-01-08T00:00:00.000000Z"
      }
    ],
    "total_sections": 9
  },
  "message": "Main sections retrieved successfully"
}
```

## Content Types

### 1. Articles
- **Get All Articles**: `GET /api/articles`
- **Get Article by ID**: `GET /api/articles/{id}`
- **Get New Articles**: `GET /api/articles/new/latest`
- **Get Priority Articles**: `GET /api/articles/priority/featured`
- **Get Articles by Category**: `GET /api/articles/category/{categoryId}`

### 2. Books
- **Get All Books**: `GET /api/books`
- **Get Book by ID**: `GET /api/books/{id}`
- **Get New Books**: `GET /api/books/new/latest`
- **Get Priority Books**: `GET /api/books/priority/featured`
- **Get Books by Category**: `GET /api/books/category/{categoryId}`
- **Get Books by Format**: `GET /api/books/format/{format}` (pdf, epub, kfx)

### 3. Videos
- **Get All Videos**: `GET /api/videos`
- **Get Video by ID**: `GET /api/videos/{id}`
- **Get New Videos**: `GET /api/videos/new/latest`
- **Get Priority Videos**: `GET /api/videos/priority/featured`
- **Get Videos by Category**: `GET /api/videos/category/{categoryId}`
- **Get YouTube Videos**: `GET /api/videos/youtube/all`

### 4. Sounds
- **Get All Sounds**: `GET /api/sounds`
- **Get Sound by ID**: `GET /api/sounds/{id}`
- **Get New Sounds**: `GET /api/sounds/new/latest`
- **Get Priority Sounds**: `GET /api/sounds/priority/featured`
- **Get Sounds by Category**: `GET /api/sounds/category/{categoryId}`

### 5. Pages
- **Get All Pages**: `GET /api/pages`
- **Get Page by ID**: `GET /api/pages/{id}`
- **Get New Pages**: `GET /api/pages/new/latest`
- **Get Priority Pages**: `GET /api/pages/priority/featured`

### 6. Blocks
- **Get All Blocks**: `GET /api/blocks`
- **Get Block by ID**: `GET /api/blocks/{id}`
- **Get New Blocks**: `GET /api/blocks/new/latest`
- **Get Priority Blocks**: `GET /api/blocks/priority/featured`

### 7. Links
- **Get All Links**: `GET /api/links`
- **Get Link by ID**: `GET /api/links/{id}`
- **Get Links by Menu**: `GET /api/links/menu/{menuId}`

## Categories

Each content type has its own category system:

### Article Categories
- **Get All Categories**: `GET /api/article-categories`
- **Get Category by ID**: `GET /api/article-categories/{id}`
- **Get Main Categories**: `GET /api/article-categories/main/parents`
- **Get Subcategories**: `GET /api/article-categories/subcategories/{parentId}`
- **Get Category Hierarchy**: `GET /api/article-categories/hierarchy/tree`

### Book Categories
- **Get All Categories**: `GET /api/book-categories`
- **Get Category by ID**: `GET /api/book-categories/{id}`
- **Get Main Categories**: `GET /api/book-categories/main/parents`
- **Get Subcategories**: `GET /api/book-categories/subcategories/{parentId}`
- **Get Category Hierarchy**: `GET /api/book-categories/hierarchy/tree`

### Video Categories
- **Get All Categories**: `GET /api/video-categories`
- **Get Category by ID**: `GET /api/video-categories/{id}`
- **Get Main Categories**: `GET /api/video-categories/main/parents`
- **Get Subcategories**: `GET /api/video-categories/subcategories/{parentId}`
- **Get Category Hierarchy**: `GET /api/video-categories/hierarchy/tree`

### Sound Categories
- **Get All Categories**: `GET /api/sound-categories`
- **Get Category by ID**: `GET /api/sound-categories/{id}`
- **Get Main Categories**: `GET /api/sound-categories/main/parents`
- **Get Subcategories**: `GET /api/sound-categories/subcategories/{parentId}`
- **Get Category Hierarchy**: `GET /api/sound-categories/hierarchy/tree`

## Menus

### Get All Menus
```http
GET /api/menus
```

### Get Menu by ID
```http
GET /api/menus/{id}
```

### Get Menus by Position
```http
GET /api/menus/position/{position}
```

### Get Header Menus
```http
GET /api/menus/header/active
```

## Response Format

All API responses follow this format:

```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Success message"
}
```

For paginated responses:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      // Array of items
    ],
    "first_page_url": "...",
    "from": 1,
    "last_page": 10,
    "last_page_url": "...",
    "next_page_url": "...",
    "path": "...",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 150
  },
  "message": "Data retrieved successfully"
}
```

## Error Handling

Error responses follow this format:

```json
{
  "success": false,
  "message": "Error description",
  "error_details": {
    "file": "File path",
    "line": "Line number"
  }
}
```

## Database Structure

The API is built on a MySQL database with the following main tables:

- **st_menus**: Main navigation menus
- **st_article**: Articles content
- **st_article_category**: Article categories
- **st_book**: Books content
- **st_book_category**: Book categories
- **st_video**: Videos content
- **st_video_category**: Video categories
- **st_sound**: Audio content
- **st_sound_category**: Sound categories
- **st_photo_gallery**: Photo galleries
- **st_photo_gallery_category**: Photo gallery categories
- **st_pages**: Static pages
- **st_blocks**: Content blocks
- **st_links**: Navigation links

## Features

- **Read-only API**: No data modification operations
- **Pagination**: Automatic pagination for large datasets
- **Filtering**: Filter by category, status, priority, etc.
- **Hierarchical Data**: Support for parent-child relationships
- **Multi-language**: Support for Arabic content
- **Content Types**: Articles, books, videos, sounds, galleries, pages
- **Categories**: Hierarchical category system for each content type

## Usage Examples

### Get Main Sections
```bash
curl http://localhost:8000/api/sections-simple
```

### Get All Articles
```bash
curl http://localhost:8000/api/articles
```

### Get Articles by Category
```bash
curl http://localhost:8000/api/articles/category/1
```

### Get New Books
```bash
curl http://localhost:8000/api/books/new/latest
```

### Get YouTube Videos
```bash
curl http://localhost:8000/api/videos/youtube/all
```

## Postman Collection

A complete Postman collection is available in `Siraj_Al_Din_API_Collection.json` with all working endpoints organized by content type.

## Notes

- The API is designed for read-only access
- All content is in Arabic
- The system supports hierarchical categories
- Content can be filtered by various criteria (new, priority, category, etc.)
- The main sections endpoint provides the hierarchical structure between menus and pages








