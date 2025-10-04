# Category Hierarchy API Guide

## Overview
This API provides hierarchical access to categories for different content types. It allows the frontend to fetch categories based on menu IDs and navigate through category hierarchies.

## Base URL
```
http://localhost:8000/api
```

## Available Endpoints

### 1. Get Categories by Menu ID
```http
GET /api/categories/menu/{menuId}
```

**Description**: Get all categories related to a specific menu

**Parameters**:
- `menuId` (required): The ID of the menu

**Example**:
```bash
curl http://localhost:8000/api/categories/menu/46
```

**Response**:
```json
{
  "success": true,
  "data": {
    "menu": {
      "id": 46,
      "name": "السيرة الذاتية",
      "priority": 20
    },
    "categories": [
      {
        "type": "article_category",
        "id": 1,
        "title": "ما يتعلق بآيات القرآن الكريم",
        "note": "تصنيف للمقالات المتعلقة بالقرآن",
        "position": 1,
        "language": "ar",
        "date": "2019-05-13T00:00:00.000000Z",
        "parent_id": 0,
        "is_main": true,
        "show_in_menu": 1,
        "show_in_main": 1,
        "content_count": 5
      }
    ]
  },
  "message": "Categories retrieved successfully"
}
```

### 2. Get Main Categories by Type
```http
GET /api/categories/{type}/main
```

**Description**: Get all main (parent) categories for a specific content type

**Parameters**:
- `type` (required): Content type (articles, books, videos, sounds, photo_galleries)

**Example**:
```bash
curl http://localhost:8000/api/categories/articles/main
```

**Response**:
```json
{
  "success": true,
  "data": {
    "type": "articles",
    "categories": [
      {
        "id": 1,
        "title": "ما يتعلق بآيات القرآن الكريم",
        "note": "تصنيف للمقالات المتعلقة بالقرآن",
        "position": 1,
        "language": "ar",
        "date": "2019-05-13T00:00:00.000000Z",
        "menu_id": 46,
        "show_in_menu": 1,
        "show_in_main": 1,
        "content_count": 5
      }
    ],
    "total_categories": 1
  },
  "message": "Main categories retrieved successfully"
}
```

### 3. Get Subcategories
```http
GET /api/categories/{type}/{categoryId}/subcategories
```

**Description**: Get all subcategories for a specific category

**Parameters**:
- `type` (required): Content type (articles, books, videos, sounds, photo_galleries)
- `categoryId` (required): The ID of the parent category

**Example**:
```bash
curl http://localhost:8000/api/categories/articles/1/subcategories
```

**Response**:
```json
{
  "success": true,
  "data": {
    "parent_category": {
      "id": 1,
      "title": "ما يتعلق بآيات القرآن الكريم",
      "type": "articles"
    },
    "subcategories": [
      {
        "id": 2,
        "title": "تفسير الآيات",
        "note": "تصنيف فرعي لتفسير الآيات",
        "position": 1,
        "language": "ar",
        "date": "2019-05-13T00:00:00.000000Z",
        "parent_id": 1,
        "show_in_menu": 1,
        "show_in_main": 1,
        "content_count": 3
      }
    ]
  },
  "message": "Subcategories retrieved successfully"
}
```

## Content Types

The following content types are supported:

| Type | Description | Category Table | Content Table |
|------|-------------|----------------|---------------|
| `articles` | Articles | `st_article_category` | `st_article` |
| `books` | Books | `st_book_category` | `st_book` |
| `videos` | Videos | `st_video_category` | `st_video` |
| `sounds` | Audio | `st_sound_category` | `st_sound` |
| `photo_galleries` | Photo Galleries | `st_photo_gallery_category` | `st_photo_gallery` |

## Frontend Usage Examples

### 1. Get Categories for Biography Section
```javascript
// Get all categories for Biography menu (ID: 46)
fetch('/api/categories/menu/46')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Menu:', data.data.menu);
      console.log('Categories:', data.data.categories);
    }
  });
```

### 2. Get Main Article Categories
```javascript
// Get all main article categories
fetch('/api/categories/articles/main')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Article Categories:', data.data.categories);
    }
  });
```

### 3. Get Subcategories for a Specific Category
```javascript
// Get subcategories for article category ID 1
fetch('/api/categories/articles/1/subcategories')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Parent:', data.data.parent_category);
      console.log('Subcategories:', data.data.subcategories);
    }
  });
```

### 4. Dynamic Category Loading
```javascript
// Function to load categories based on menu selection
function loadCategories(menuId) {
  return fetch(`/api/categories/menu/${menuId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        return data.data.categories;
      }
      throw new Error(data.message);
    });
}

// Function to load subcategories
function loadSubcategories(type, categoryId) {
  return fetch(`/api/categories/${type}/${categoryId}/subcategories`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        return data.data.subcategories;
      }
      throw new Error(data.message);
    });
}

// Usage example
loadCategories(46).then(categories => {
  categories.forEach(category => {
    console.log(`${category.type}: ${category.title}`);
    
    // Load subcategories if needed
    if (category.content_count > 0) {
      loadSubcategories(category.type, category.id)
        .then(subcategories => {
          console.log(`Subcategories for ${category.title}:`, subcategories);
        });
    }
  });
});
```

## Response Structure

### Category Object
```json
{
  "type": "article_category",
  "id": 1,
  "title": "ما يتعلق بآيات القرآن الكريم",
  "note": "تصنيف للمقالات المتعلقة بالقرآن",
  "position": 1,
  "language": "ar",
  "date": "2019-05-13T00:00:00.000000Z",
  "parent_id": 0,
  "is_main": true,
  "show_in_menu": 1,
  "show_in_main": 1,
  "content_count": 5
}
```

### Menu Object
```json
{
  "id": 46,
  "name": "السيرة الذاتية",
  "priority": 20
}
```

## Error Handling

### Menu Not Found
```json
{
  "success": false,
  "message": "Menu not found"
}
```

### Category Not Found
```json
{
  "success": false,
  "message": "Category not found"
}
```

### Invalid Category Type
```json
{
  "success": false,
  "message": "Invalid category type"
}
```

### Server Error
```json
{
  "success": false,
  "message": "Error: [error details]"
}
```

## Available Menu IDs

| Menu ID | Name | Description |
|---------|------|-------------|
| 46 | السيرة الذاتية | Biography |
| 21 | كُتُب الإمام | Imam's Books |
| 40 | فوائد وفتاوى | Benefits and Fatwas |
| 54 | الفيديوهات | Videos |
| 55 | الصوتيات | Audio |
| 63 | معرض الصور | Photo Gallery |
| 48 | الرئيسية | Main/Home |
| 53 | الكتب والمؤلفات | Books and Authors |
| 36 | تواصل معنا | Contact Us |

## Features

- **Hierarchical Structure**: Support for parent-child category relationships
- **Multiple Content Types**: Support for articles, books, videos, sounds, and photo galleries
- **Menu-based Filtering**: Get categories related to specific menus
- **Content Counting**: Each category includes the count of related content
- **Position Sorting**: Categories are sorted by their position
- **Language Support**: Full support for Arabic content
- **Active Filtering**: Only active categories are returned

## Notes

- All categories are filtered to show only active ones
- Categories are sorted by position (ascending)
- Content counts are calculated dynamically
- The API supports both direct menu relationships and content type filtering
- All responses include success status and descriptive messages





