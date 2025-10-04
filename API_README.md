# Siraj Al-Din API Documentation

## Overview
This API provides access to Islamic content including articles, books, videos, sounds, photo galleries, and more from the Siraj Al-Din content management system.

## Base URL
```
http://localhost:8000/api
```

## Authentication
Currently, no authentication is required as this is a read-only API for content display.

## Response Format
All API responses follow this format:
```json
{
  "success": true,
  "data": [...],
  "message": "Success message"
}
```

## Error Format
```json
{
  "success": false,
  "message": "Error message"
}
```

## API Endpoints

### 1. Health Check
- **GET** `/health` - Check API status

### 2. Menus
- **GET** `/menus` - Get all active menus
- **GET** `/menus/{id}` - Get menu by ID
- **GET** `/menus/position/{position}` - Get menus by position
- **GET** `/menus/header/active` - Get header menus

### 3. Articles
- **GET** `/articles` - Get all articles
- **GET** `/articles/{id}` - Get article by ID
- **GET** `/articles/new/latest` - Get new articles
- **GET** `/articles/priority/featured` - Get priority articles
- **GET** `/articles/category/{categoryId}` - Get articles by category

### 4. Article Categories
- **GET** `/article-categories` - Get all categories
- **GET** `/article-categories/{id}` - Get category by ID
- **GET** `/article-categories/main/parents` - Get main categories
- **GET** `/article-categories/subcategories/{parentId}` - Get subcategories
- **GET** `/article-categories/hierarchy/tree` - Get category hierarchy

### 5. Books
- **GET** `/books` - Get all books
- **GET** `/books/{id}` - Get book by ID
- **GET** `/books/new/latest` - Get new books
- **GET** `/books/priority/featured` - Get priority books
- **GET** `/books/category/{categoryId}` - Get books by category
- **GET** `/books/format/{format}` - Get books by format (pdf, epub, kfx)

### 6. Book Categories
- **GET** `/book-categories` - Get all categories
- **GET** `/book-categories/{id}` - Get category by ID
- **GET** `/book-categories/main/parents` - Get main categories
- **GET** `/book-categories/subcategories/{parentId}` - Get subcategories
- **GET** `/book-categories/hierarchy/tree` - Get category hierarchy

### 7. Videos
- **GET** `/videos` - Get all videos
- **GET** `/videos/{id}` - Get video by ID
- **GET** `/videos/new/latest` - Get new videos
- **GET** `/videos/priority/featured` - Get priority videos
- **GET** `/videos/category/{categoryId}` - Get videos by category
- **GET** `/videos/youtube/all` - Get YouTube videos

### 8. Video Categories
- **GET** `/video-categories` - Get all categories
- **GET** `/video-categories/{id}` - Get category by ID
- **GET** `/video-categories/main/parents` - Get main categories
- **GET** `/video-categories/subcategories/{parentId}` - Get subcategories
- **GET** `/video-categories/hierarchy/tree` - Get category hierarchy

### 9. Sounds
- **GET** `/sounds` - Get all sounds
- **GET** `/sounds/{id}` - Get sound by ID
- **GET** `/sounds/new/latest` - Get new sounds
- **GET** `/sounds/priority/featured` - Get priority sounds
- **GET** `/sounds/category/{categoryId}` - Get sounds by category

### 10. Sound Categories
- **GET** `/sound-categories` - Get all categories
- **GET** `/sound-categories/{id}` - Get category by ID
- **GET** `/sound-categories/main/parents` - Get main categories
- **GET** `/sound-categories/subcategories/{parentId}` - Get subcategories
- **GET** `/sound-categories/hierarchy/tree` - Get category hierarchy

### 11. Photo Galleries
- **GET** `/photo-galleries` - Get all photo galleries
- **GET** `/photo-galleries/{id}` - Get photo gallery by ID
- **GET** `/photo-galleries/new/latest` - Get new photo galleries
- **GET** `/photo-galleries/priority/featured` - Get priority photo galleries
- **GET** `/photo-galleries/category/{categoryId}` - Get photo galleries by category

### 12. Photo Gallery Categories
- **GET** `/photo-gallery-categories` - Get all categories
- **GET** `/photo-gallery-categories/{id}` - Get category by ID
- **GET** `/photo-gallery-categories/main/parents` - Get main categories
- **GET** `/photo-gallery-categories/subcategories/{parentId}` - Get subcategories
- **GET** `/photo-gallery-categories/hierarchy/tree` - Get category hierarchy

### 13. Pages
- **GET** `/pages` - Get all pages
- **GET** `/pages/{id}` - Get page by ID
- **GET** `/pages/new/latest` - Get new pages
- **GET** `/pages/priority/featured` - Get priority pages

### 14. Blocks
- **GET** `/blocks` - Get all blocks
- **GET** `/blocks/{id}` - Get block by ID
- **GET** `/blocks/new/latest` - Get new blocks
- **GET** `/blocks/priority/featured` - Get priority blocks

### 15. Links
- **GET** `/links` - Get all links
- **GET** `/links/{id}` - Get link by ID
- **GET** `/links/menu/{menuId}` - Get links by menu

### 16. Dashboard
- **GET** `/dashboard/summary` - Get dashboard summary

## Query Parameters

### Common Parameters
- `per_page` - Number of items per page (default: 15)
- `search` - Search term for filtering
- `language` - Language filter (default: 'ar')
- `is_new` - Filter for new items (true/false)
- `is_priority` - Filter for priority items (true/false)

### Category Parameters
- `category_id` - Filter by category ID
- `menu_id` - Filter by menu ID
- `main_only` - Get only main categories (true/false)

### Position Parameters
- `position` - Menu position (bottom_header, top_footer, left, right)

## Examples

### Get all articles with pagination
```
GET /api/articles?per_page=10&page=1
```

### Search articles
```
GET /api/articles?search=قرآن
```

### Get articles by category
```
GET /api/articles/category/1?per_page=20
```

### Get new books
```
GET /api/books/new/latest
```

### Get YouTube videos
```
GET /api/videos/youtube/all
```

### Get books in PDF format
```
GET /api/books/format/pdf
```

### Get main categories
```
GET /api/article-categories/main/parents
```

### Get category hierarchy
```
GET /api/article-categories/hierarchy/tree
```

## Postman Collection
Import the `postman_collection.json` file into Postman to test all endpoints easily.

## Database Structure
The API is built on the following main tables:
- `st_menus` - Navigation menus
- `st_links` - Menu links
- `st_article` - Articles content
- `st_article_category` - Article categories
- `st_book` - Books content
- `st_book_category` - Book categories
- `st_video` - Video content
- `st_video_category` - Video categories
- `st_sound` - Audio content
- `st_sound_category` - Sound categories
- `st_photo_gallery` - Photo galleries
- `st_photo_gallery_category` - Photo gallery categories
- `st_pages` - Static pages
- `st_blocks` - Content blocks

## Features
- ✅ Read-only API (no data modification)
- ✅ Pagination support
- ✅ Search functionality
- ✅ Category filtering
- ✅ Language support
- ✅ Priority/featured content
- ✅ New content filtering
- ✅ Hierarchical categories
- ✅ Visitor tracking
- ✅ Multiple file format support
- ✅ YouTube integration





