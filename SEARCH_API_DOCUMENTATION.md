# 🔍 Search API Documentation - دليل API البحث

## نظرة عامة - Overview

تم تطوير ميزة بحث شاملة تسمح بالبحث في جميع أقسام المشروع (المقالات، الكتب، الفيديوهات، الأصوات، معارض الصور، والصفحات) مع دعم متقدم للبحث باللغة العربية.

## 📋 المحتويات - Table of Contents

- [Endpoints](#endpoints)
- [البحث الشامل](#global-search)
- [اقتراحات البحث](#search-suggestions)
- [المعاملات](#parameters)
- [أمثلة الاستخدام](#usage-examples)
- [الاستجابة](#response-format)
- [أمثلة متقدمة](#advanced-examples)

---

## 🚀 Endpoints

### 1. البحث الشامل
```
GET /api/search
```

### 2. اقتراحات البحث
```
GET /api/search/suggestions
```

---

## 🔍 البحث الشامل - Global Search

### الوصف
يسمح بالبحث في جميع أنواع المحتوى أو في نوع محدد مع دعم ترتيب النتائج وتصفيتها.

### المعاملات - Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `q` | string | ✅ | - | كلمة أو عبارة البحث |
| `type` | string | ❌ | `all` | نوع المحتوى: `all`, `articles`, `books`, `videos`, `sounds`, `photo_galleries`, `pages` |
| `sort` | string | ❌ | `relevance` | ترتيب النتائج: `relevance`, `date`, `popularity`, `priority` |
| `filters` | string | ❌ | - | فلاتر: `priority`, `new`, `popular` (مفصولة بفاصلة) |
| `per_page` | integer | ❌ | 15 | عدد النتائج لكل صفحة |

### أمثلة الاستخدام

#### البحث في جميع الأقسام
```bash
GET /api/search?q=القرآن
```

#### البحث في مقالات فقط
```bash
GET /api/search?q=الصلاة&type=articles
```

#### البحث مع ترتيب حسب التاريخ
```bash
GET /api/search?q=الإسلام&sort=date
```

#### البحث مع فلاتر
```bash
GET /api/search?q=الدعاء&filters=priority,new
```

#### بحث متقدم
```bash
GET /api/search?q=النبي محمد&type=all&sort=relevance&filters=priority&per_page=20
```

---

## 💡 اقتراحات البحث - Search Suggestions

### الوصف
يوفر اقتراحات للبحث بناءً على المحتوى الموجود.

### المعاملات - Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `q` | string | ✅ | - | كلمة البحث (الحد الأدنى حرفين) |

### أمثلة الاستخدام

```bash
GET /api/search/suggestions?q=قر
```

---

## 📊 تنسيق الاستجابة - Response Format

### البحث الشامل

```json
{
  "success": true,
  "data": {
    "keyword": "القرآن",
    "search_type": "all",
    "sort_by": "relevance",
    "filters": "priority,new",
    "total_results": 25,
    "results": {
      "articles": {
        "data": [
          {
            "id": 1,
            "title": "عنوان المقال",
            "summary": "ملخص المقال",
            "description": "وصف المقال",
            "picture": "صورة المقال",
            "visitor_count": 150,
            "is_new": false,
            "priority": 5,
            "date": "2024-01-15T10:30:00.000000Z",
            "category": {
              "id": 1,
              "name": "اسم التصنيف"
            },
            "type": "article",
            "type_label": "مقال",
            "relevance_score": 85
          }
        ],
        "count": 5,
        "label": "المقالات"
      },
      "books": {
        "data": [...],
        "count": 3,
        "label": "الكتب"
      },
      "videos": {
        "data": [...],
        "count": 2,
        "label": "الفيديوهات"
      },
      "sounds": {
        "data": [...],
        "count": 4,
        "label": "الأصوات"
      },
      "photo_galleries": {
        "data": [...],
        "count": 1,
        "label": "معارض الصور"
      },
      "pages": {
        "data": [...],
        "count": 10,
        "label": "الصفحات"
      }
    }
  },
  "message": "Search completed successfully"
}
```

### اقتراحات البحث

```json
{
  "success": true,
  "data": [
    "إن القرآن أنزل على سبعة أحرف",
    "تلاوة القرآن المجيد",
    "هدي القرآن الكريم إلى الحجة والبرهان"
  ],
  "message": "Suggestions retrieved successfully"
}
```

---

## 🎯 الميزات المتقدمة - Advanced Features

### 1. معالجة النص العربي
- إزالة الكلمات الوظيفية (في، من، إلى، على، هذا، هذه)
- دعم البحث بالعبارة الكاملة أو الكلمات المنفصلة
- حساب نقاط الصلة للمحتوى

### 2. خوارزمية الصلة
- **العنوان**: 10 نقاط
- **الملخص**: 5 نقاط  
- **الوصف**: 3 نقاط
- **العبارة الكاملة**: نقاط مضاعفة

### 3. الفلاتر المتاحة
- `priority`: المحتوى ذو الأولوية العالية
- `new`: المحتوى الجديد
- `popular`: المحتوى الأكثر زيارة

### 4. خيارات الترتيب
- `relevance`: حسب الصلة (افتراضي)
- `date`: حسب التاريخ
- `popularity`: حسب عدد الزيارات
- `priority`: حسب الأولوية

---

## 🌐 أمثلة متقدمة - Advanced Examples

### JavaScript Frontend Integration

```javascript
// البحث الأساسي
async function searchContent(keyword) {
  try {
    const response = await fetch(`/api/search?q=${encodeURIComponent(keyword)}`);
    const data = await response.json();
    
    if (data.success) {
      console.log('Total results:', data.data.total_results);
      console.log('Articles:', data.data.results.articles.data);
      console.log('Books:', data.data.results.books.data);
    }
  } catch (error) {
    console.error('Search error:', error);
  }
}

// البحث المتقدم مع فلاتر
async function advancedSearch(keyword, type, sort, filters) {
  const params = new URLSearchParams({
    q: keyword,
    type: type,
    sort: sort,
    filters: filters.join(',')
  });
  
  const response = await fetch(`/api/search?${params}`);
  return await response.json();
}

// الحصول على اقتراحات
async function getSuggestions(keyword) {
  if (keyword.length < 2) return [];
  
  const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(keyword)}`);
  const data = await response.json();
  
  return data.success ? data.data : [];
}

// استخدام المثال
searchContent('القرآن الكريم');
advancedSearch('الصلاة', 'articles', 'date', ['priority', 'new']);
getSuggestions('قر');
```

### React Component Example

```jsx
import React, { useState, useEffect } from 'react';

const SearchComponent = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [results, setResults] = useState(null);
  const [suggestions, setSuggestions] = useState([]);
  const [loading, setLoading] = useState(false);

  // البحث
  const handleSearch = async (term, type = 'all', sort = 'relevance') => {
    setLoading(true);
    try {
      const response = await fetch(
        `/api/search?q=${encodeURIComponent(term)}&type=${type}&sort=${sort}`
      );
      const data = await response.json();
      
      if (data.success) {
        setResults(data.data);
      }
    } catch (error) {
      console.error('Search error:', error);
    } finally {
      setLoading(false);
    }
  };

  // اقتراحات البحث
  useEffect(() => {
    if (searchTerm.length >= 2) {
      const timeoutId = setTimeout(async () => {
        try {
          const response = await fetch(
            `/api/search/suggestions?q=${encodeURIComponent(searchTerm)}`
          );
          const data = await response.json();
          
          if (data.success) {
            setSuggestions(data.data);
          }
        } catch (error) {
          console.error('Suggestions error:', error);
        }
      }, 300);

      return () => clearTimeout(timeoutId);
    } else {
      setSuggestions([]);
    }
  }, [searchTerm]);

  return (
    <div className="search-container">
      <input
        type="text"
        value={searchTerm}
        onChange={(e) => setSearchTerm(e.target.value)}
        placeholder="ابحث في المحتوى..."
        className="search-input"
      />
      
      {suggestions.length > 0 && (
        <div className="suggestions">
          {suggestions.map((suggestion, index) => (
            <div
              key={index}
              className="suggestion-item"
              onClick={() => {
                setSearchTerm(suggestion);
                handleSearch(suggestion);
              }}
            >
              {suggestion}
            </div>
          ))}
        </div>
      )}
      
      <button
        onClick={() => handleSearch(searchTerm)}
        disabled={loading}
        className="search-button"
      >
        {loading ? 'جاري البحث...' : 'بحث'}
      </button>
      
      {results && (
        <div className="search-results">
          <h3>نتائج البحث ({results.total_results})</h3>
          
          {Object.entries(results.results).map(([type, section]) => (
            section.count > 0 && (
              <div key={type} className="result-section">
                <h4>{section.label} ({section.count})</h4>
                {section.data.map((item) => (
                  <div key={item.id} className="result-item">
                    <h5>{item.title}</h5>
                    <p>{item.summary}</p>
                    <span className="relevance-score">
                      الصلة: {item.relevance_score}%
                    </span>
                  </div>
                ))}
              </div>
            )
          ))}
        </div>
      )}
    </div>
  );
};

export default SearchComponent;
```

---

## ⚡ نصائح الأداء - Performance Tips

1. **استخدم pagination**: حدد `per_page` لتجنب النتائج الكثيرة
2. **اقتراحات البحث**: استخدم debouncing لتجنب طلبات كثيرة
3. **تخزين مؤقت**: احفظ النتائج محلياً لتجربة أفضل
4. **فلاتر ذكية**: استخدم الفلاتر لتضييق النتائج

---

## 🐛 معالجة الأخطاء - Error Handling

### أخطاء شائعة

```json
{
  "success": false,
  "message": "Search keyword is required. Use ?q=keyword",
  "error_details": {
    "file": "/path/to/file.php",
    "line": 45
  }
}
```

### رموز الحالة
- `200`: نجح البحث
- `400`: معاملات غير صحيحة
- `500`: خطأ في الخادم

---

## 📱 دعم الهواتف المحمولة

الـ API يدعم تماماً التطبيقات المحمولة ويوفر:
- استجابات JSON محسنة
- دعم UTF-8 للعربية
- معالجة سريعة للاستعلامات

---

## 🔧 التخصيص - Customization

يمكن تخصيص البحث من خلال:
- تعديل أوزان الصلة في `SearchController`
- إضافة حقول بحث جديدة
- تخصيص معالجة النص العربي
- إضافة فلاتر جديدة

---

## 📞 الدعم - Support

للحصول على المساعدة أو الإبلاغ عن مشاكل:
- راجع التوثيق أعلاه
- تأكد من صحة المعاملات
- تحقق من تنسيق الاستجابة

---

**تم تطوير هذا الـ API بواسطة فريق التطوير** 🚀
