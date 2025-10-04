# Siraj Al Din - Complete API Usage Guide

## 🏠 Home & Main Sections

### 1. Get All Menus (Home Page)
```
GET /api/menus
```
**Description**: جلب جميع الأقسام الرئيسية للصفحة الرئيسية

### 2. Get Active Header Menus
```
GET /api/menus/header/active
```
**Description**: جلب الأقسام النشطة في الهيدر فقط

---

## 📰 Articles Section

### 1. Get All Article Categories
```
GET /api/categories/articles/main
```
**Description**: جلب جميع التصنيفات الرئيسية للمقالات

### 2. Get Article Subcategories
```
GET /api/categories/articles/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين
**Example**: `/api/categories/articles/1/subcategories`

### 3. Get Articles by Category
```
GET /api/categories/articles/{categoryId}/content
```
**Description**: جلب جميع المقالات في تصنيف معين
**Example**: `/api/categories/articles/1/content`

### 4. Get All Articles
```
GET /api/articles
```
**Description**: جلب جميع المقالات

### 5. Get Article by ID
```
GET /api/articles/{id}
```
**Description**: جلب مقال محدد بالـ ID
**Example**: `/api/articles/1`

### 6. Get New Articles
```
GET /api/articles/new/latest
```
**Description**: جلب أحدث المقالات الجديدة

### 7. Get Priority Articles
```
GET /api/articles/priority/featured
```
**Description**: جلب المقالات المميزة

---

## 📚 Books Section

### 1. Get All Book Categories
```
GET /api/categories/books/main
```
**Description**: جلب جميع التصنيفات الرئيسية للكتب

### 2. Get Book Subcategories
```
GET /api/categories/books/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين

### 3. Get Books by Category
```
GET /api/categories/books/{categoryId}/content
```
**Description**: جلب جميع الكتب في تصنيف معين

### 4. Get All Books
```
GET /api/books
```
**Description**: جلب جميع الكتب

### 5. Get Book by ID
```
GET /api/books/{id}
```
**Description**: جلب كتاب محدد بالـ ID

### 6. Get Books by Format
```
GET /api/books/format/{format}
```
**Description**: جلب الكتب بصيغة معينة
**Available formats**: `pdf`, `epub`, `kfx`

---

## 🎥 Videos Section

### 1. Get All Video Categories
```
GET /api/categories/videos/main
```
**Description**: جلب جميع التصنيفات الرئيسية للفيديوهات

### 2. Get Video Subcategories
```
GET /api/categories/videos/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين

### 3. Get Videos by Category
```
GET /api/categories/videos/{categoryId}/content
```
**Description**: جلب جميع الفيديوهات في تصنيف معين

### 4. Get All Videos
```
GET /api/videos
```
**Description**: جلب جميع الفيديوهات

### 5. Get Video by ID
```
GET /api/videos/{id}
```
**Description**: جلب فيديو محدد بالـ ID

### 6. Get YouTube Videos
```
GET /api/videos/youtube/all
```
**Description**: جلب جميع الفيديوهات التي تحتوي على YouTube ID

---

## 🎵 Sounds Section

### 1. Get All Sound Categories
```
GET /api/categories/sounds/main
```
**Description**: جلب جميع التصنيفات الرئيسية للأصوات

### 2. Get Sound Subcategories
```
GET /api/categories/sounds/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين

### 3. Get Sounds by Category
```
GET /api/categories/sounds/{categoryId}/content
```
**Description**: جلب جميع الأصوات في تصنيف معين

### 4. Get All Sounds
```
GET /api/sounds
```
**Description**: جلب جميع الأصوات

### 5. Get Sound by ID
```
GET /api/sounds/{id}
```
**Description**: جلب صوت محدد بالـ ID

---

## 🖼️ Photo Galleries Section

### 1. Get All Photo Gallery Categories
```
GET /api/categories/photo_galleries/main
```
**Description**: جلب جميع التصنيفات الرئيسية للمعرض الصور

### 2. Get Photo Gallery Subcategories
```
GET /api/categories/photo_galleries/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين

### 3. Get Photo Galleries by Category
```
GET /api/categories/photo_galleries/{categoryId}/content
```
**Description**: جلب جميع معارض الصور في تصنيف معين

### 4. Get All Photo Galleries
```
GET /api/photo-galleries
```
**Description**: جلب جميع معارض الصور

### 5. Get Photo Gallery by ID
```
GET /api/photo-galleries/{id}
```
**Description**: جلب معرض صور محدد بالـ ID

---

## 📄 Pages Section

### 1. Get All Pages
```
GET /api/pages
```
**Description**: جلب جميع الصفحات

### 2. Get Page by ID
```
GET /api/pages/{id}
```
**Description**: جلب صفحة محددة بالـ ID

### 3. Get New Pages
```
GET /api/pages/new/latest
```
**Description**: جلب أحدث الصفحات الجديدة

### 4. Get Priority Pages
```
GET /api/pages/priority/featured
```
**Description**: جلب الصفحات المميزة

---

## 🔗 Links Section

### 1. Get All Links
```
GET /api/links
```
**Description**: جلب جميع الروابط

### 2. Get Link by ID
```
GET /api/links/{id}
```
**Description**: جلب رابط محدد بالـ ID

### 3. Get Links by Menu
```
GET /api/links/menu/{menuId}
```
**Description**: جلب الروابط لقسم معين

---

## 🔧 Dynamic Category Hierarchy

### 1. Get Categories by Menu ID
```
GET /api/categories/menu/{menuId}
```
**Description**: جلب جميع التصنيفات المرتبطة بقسم معين
**Available Menu IDs**: 46, 21, 40, 54, 55, 63, 48, 53, 36

### 2. Get Main Categories by Type
```
GET /api/categories/{type}/main
```
**Description**: جلب التصنيفات الرئيسية لنوع محتوى معين
**Available Types**: `articles`, `books`, `videos`, `sounds`, `photo_galleries`

### 3. Get Subcategories
```
GET /api/categories/{type}/{categoryId}/subcategories
```
**Description**: جلب التصنيفات الفرعية لتصنيف معين

### 4. Get Content by Category
```
GET /api/categories/{type}/{categoryId}/content
```
**Description**: جلب محتويات تصنيف معين
**Example**: `/api/categories/articles/1/content`

---

## 📊 Dashboard & Statistics

### 1. Get Dashboard Summary
```
GET /api/dashboard/summary
```
**Description**: جلب إحصائيات عامة وعدد المحتويات

### 2. Health Check
```
GET /api/health
```
**Description**: فحص حالة الـ API

---

## 🚀 Quick Start Examples

### 1. Home Page Flow
```
1. GET /api/menus (Get main sections)
2. GET /api/categories/menu/{menuId} (Get categories for each section)
3. GET /api/categories/{type}/{categoryId}/content (Get content for each category)
```

### 2. Article Section Flow
```
1. GET /api/categories/articles/main (Get article categories)
2. GET /api/categories/articles/1/content (Get articles in category 1)
3. GET /api/articles/123 (Get specific article)
```

### 3. Book Section Flow
```
1. GET /api/categories/books/main (Get book categories)
2. GET /api/categories/books/1/content (Get books in category 1)
3. GET /api/books/456 (Get specific book)
```

---

## 📝 Response Format

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

Error responses:
```json
{
    "success": false,
    "message": "Error message"
}
```

---

## 🔧 Configuration

### Base URL
```
http://localhost:8000/api
```

### Available Menu IDs
- 46: السيرة الذاتية
- 21: الملفات الصوتية
- 40: الفيديوهات
- 54: الكتب
- 55: المقالات
- 63: معرض الصور
- 48: الصفحات
- 53: الروابط
- 36: أخرى

### Content Types
- `articles`: المقالات
- `books`: الكتب
- `videos`: الفيديوهات
- `sounds`: الأصوات
- `photo_galleries`: معارض الصور

---

## 📱 Frontend Integration

### 1. Home Page
```javascript
// Get main sections
const menus = await fetch('/api/menus').then(r => r.json());

// For each menu, get its categories
for (const menu of menus.data) {
    const categories = await fetch(`/api/categories/menu/${menu.id}`).then(r => r.json());
    // Display categories
}
```

### 2. Category Page
```javascript
// Get articles in a category
const articles = await fetch('/api/categories/articles/1/content').then(r => r.json());

// Display articles
articles.data.content.forEach(article => {
    // Show article title, summary, etc.
});
```

### 3. Content Detail Page
```javascript
// Get specific article
const article = await fetch('/api/articles/123').then(r => r.json());

// Display article details
console.log(article.data.title);
console.log(article.data.content);
```








