<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\ArticleCategory;
use App\Models\BookCategory;
use App\Models\VideoCategory;
use App\Models\SoundCategory;
use App\Models\PhotoGalleryCategory;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class CategoryHierarchyController extends Controller
{
    /**
     * Get subcategories for a specific menu
     * GET /api/categories/menu/{menuId}
     */
    public function getByMenu($menuId): JsonResponse
    {
        try {
            // Get the menu
            $menu = Menu::active()->find($menuId);
            
            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu not found'
                ], 404);
            }

            $categories = [
                'menu' => [
                    'id' => $menu->menus_id,
                    'name' => $menu->menus_name,
                    'priority' => $menu->menus_priority
                ],
                'categories' => []
            ];

            // Get article categories related to this menu
            $articleCategories = ArticleCategory::active()
                ->where('cat_menus', $menuId)
                ->orderBy('cat_pos', 'asc')
                ->get();

            foreach ($articleCategories as $category) {
                $categories['categories'][] = [
                    'type' => 'article_category',
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'parent_id' => $category->cat_father_id,
                    'is_main' => $category->cat_father_id == 0,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount('articles', $category->cat_id)
                ];
            }

            // Get book categories related to this menu
            $bookCategories = BookCategory::active()
                ->where('cat_menus', $menuId)
                ->orderBy('cat_pos', 'asc')
                ->get();

            foreach ($bookCategories as $category) {
                $categories['categories'][] = [
                    'type' => 'book_category',
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'parent_id' => $category->cat_father_id,
                    'is_main' => $category->cat_father_id == 0,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount('books', $category->cat_id)
                ];
            }

            // Get video categories related to this menu
            $videoCategories = VideoCategory::active()
                ->where('cat_menus', $menuId)
                ->orderBy('cat_pos', 'asc')
                ->get();

            foreach ($videoCategories as $category) {
                $categories['categories'][] = [
                    'type' => 'video_category',
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'parent_id' => $category->cat_father_id,
                    'is_main' => $category->cat_father_id == 0,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount('videos', $category->cat_id)
                ];
            }

            // Get sound categories related to this menu
            $soundCategories = SoundCategory::active()
                ->where('cat_menus', $menuId)
                ->orderBy('cat_pos', 'asc')
                ->get();

            foreach ($soundCategories as $category) {
                $categories['categories'][] = [
                    'type' => 'sound_category',
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'parent_id' => $category->cat_father_id,
                    'is_main' => $category->cat_father_id == 0,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount('sounds', $category->cat_id)
                ];
            }

            // Get photo gallery categories related to this menu
            $galleryCategories = PhotoGalleryCategory::where('cat_active', 1)
                ->where('cat_menus', $menuId)
                ->orderBy('cat_pos', 'asc')
                ->get();

            foreach ($galleryCategories as $category) {
                $categories['categories'][] = [
                    'type' => 'photo_gallery_category',
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'parent_id' => $category->cat_father_id,
                    'is_main' => $category->cat_father_id == 0,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount('photo-galleries', $category->cat_id)
                ];
            }

            // Sort categories by position
            usort($categories['categories'], function($a, $b) {
                return $a['position'] - $b['position'];
            });

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subcategories for a specific category
     * GET /api/categories/{type}/{categoryId}/subcategories
     */
    public function getSubcategories($type, $categoryId): JsonResponse
    {
        try {
            $model = $this->getCategoryModel($type);
            
            if (!$model) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category type'
                ], 400);
            }

            if ($type === 'photo-galleries') {
                $category = $model::where('cat_active', 1)->find($categoryId);
            } else {
                $category = $model::active()->find($categoryId);
            }
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            if ($type === 'photo-galleries') {
                $subcategories = $model::where('cat_active', 1)
                    ->where('cat_father_id', $categoryId)
                    ->orderBy('cat_pos', 'asc')
                    ->get();
            } else {
                $subcategories = $model::active()
                    ->where('cat_father_id', $categoryId)
                    ->orderBy('cat_pos', 'asc')
                    ->get();
            }

            $result = [
                'parent_category' => [
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'type' => $type
                ],
                'subcategories' => []
            ];

            foreach ($subcategories as $subcategory) {
                $result['subcategories'][] = [
                    'id' => $subcategory->cat_id,
                    'title' => $subcategory->cat_title,
                    'note' => $subcategory->cat_note,
                    'position' => $subcategory->cat_pos,
                    'language' => $subcategory->cat_lan,
                    'date' => $subcategory->cat_date,
                    'parent_id' => $subcategory->cat_father_id,
                    'show_in_menu' => $subcategory->cat_show_menu,
                    'show_in_main' => $subcategory->cat_show_main,
                    'content_count' => $this->getContentCount($type, $subcategory->cat_id)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Subcategories retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all main categories for a specific content type with pagination and articles
     * GET /api/categories/{type}/main?page=1&per_page=10&include_pages=true
     */
    public function getMainCategories($type): JsonResponse
    {
        try {
            $model = $this->getCategoryModel($type);
            $contentModel = $this->getContentModel($type);
            
            if (!$model || !$contentModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category type'
                ], 400);
            }

            // Get pagination parameters
            $page = request()->get('page', 1);
            $perPage = request()->get('per_page', 10);
            $showExamples = request()->get('show_articles', true);
            $examplesPerCategory = request()->get('articles_per_category', 3);
            $includePages = request()->get('include_pages', true);
            
            // Validate pagination parameters
            $page = max(1, (int)$page);
            $perPage = max(1, min(50, (int)$perPage)); // Limit per_page to 50 max
            $examplesPerCategory = max(1, min(10, (int)$examplesPerCategory)); // Limit articles to 10 max

            // Build base query for categories
            if ($type === 'photo-galleries') {
                $query = $model::where('cat_active', 1)
                    ->where('cat_father_id', 0)
                    ->orderBy('cat_pos', 'asc');
            } else {
                $query = $model::active()
                    ->where('cat_father_id', 0)
                    ->orderBy('cat_pos', 'asc');
            }

            // Get total count for pagination
            $totalCategories = $query->count();
            $totalPages = ceil($totalCategories / $perPage);

            // Apply pagination to categories
            $categories = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $result = [];
            foreach ($categories as $category) {
                // Get articles for this category if requested
                $examples = [];
                if ($showExamples) {
                    $examples = $this->getCategoryExamples($type, $category->cat_id, $examplesPerCategory);
                }
                
                $categoryData = [
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan,
                    'date' => $category->cat_date,
                    'menu_id' => $category->cat_menus,
                    'show_in_menu' => $category->cat_show_menu,
                    'show_in_main' => $category->cat_show_main,
                    'content_count' => $this->getContentCount($type, $category->cat_id),
                    'type' => $type
                ];

                // Add data if requested
                if ($showExamples) {
                    $categoryData['data'] = $examples;
                }

                $result[] = $categoryData;
            }


            // Add pages if requested for sounds or books
            $pages = [];
            if ($includePages) {
                if ($type === 'sounds') {
                    $pages = $this->getPagesForMenu(55); // Menu ID for sounds
                } elseif ($type === 'books') {
                    $pages = $this->getPagesForMenu(21); // Menu ID for books
                }
            }

            $responseData = [
                'type' => $type,
                'categories' => $result,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_categories' => $totalCategories,
                    'total_pages' => $totalPages,
                    'has_next_page' => $page < $totalPages,
                    'has_previous_page' => $page > 1,
                    'next_page' => $page < $totalPages ? $page + 1 : null,
                    'previous_page' => $page > 1 ? $page - 1 : null
                ]
            ];

            // Add pages if available
            if (!empty($pages)) {
                $responseData['pages'] = $pages;
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'Main categories with pagination retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content by category with pagination and articles
     * GET /api/categories/{type}/{categoryId}/content?page=1&per_page=3&show_articles=true
     */
    public function getContentByCategory($type, $categoryId): JsonResponse
    {
        try {
            $categoryModel = $this->getCategoryModel($type);
            $contentModel = $this->getContentModel($type);
            
            if (!$categoryModel || !$contentModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category type'
                ], 400);
            }

            if ($type === 'photo-galleries') {
                $category = $categoryModel::where('cat_active', 1)->find($categoryId);
            } else {
                $category = $categoryModel::active()->find($categoryId);
            }
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            // Get pagination parameters
            $page = request()->get('page', 1);
            $perPage = request()->get('per_page', 3);
            $showExamples = request()->get('show_articles', true);
            
            // Validate pagination parameters
            $page = max(1, (int)$page);
            $perPage = max(1, min(50, (int)$perPage)); // Limit per_page to 50 max

            $categoryField = $this->getCategoryField($type);
            
            // Build base query
            $query = $contentModel::active()
                ->where($categoryField, $categoryId);

            // Get total count for pagination
            $totalItems = $query->count();
            $totalPages = ceil($totalItems / $perPage);

            // Apply ordering and pagination
            // For books, order by date only (oldest first)
            if ($type === 'books') {
                $content = $query->orderBy($this->getDateField($type), 'asc')
                    ->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
            } else {
                $content = $query->orderBy($this->getPriorityField($type), 'desc')
                    ->orderBy($this->getDateField($type), 'desc')
                    ->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();
            }

            $result = [
                'category' => [
                    'id' => $category->cat_id,
                    'title' => $category->cat_title,
                    'note' => $category->cat_note,
                    'type' => $type,
                    'position' => $category->cat_pos,
                    'language' => $category->cat_lan
                ],
                'content' => [],
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total_items' => $totalItems,
                    'total_pages' => $totalPages,
                    'has_next_page' => $page < $totalPages,
                    'has_previous_page' => $page > 1,
                    'next_page' => $page < $totalPages ? $page + 1 : null,
                    'previous_page' => $page > 1 ? $page - 1 : null
                ]
            ];

            // If show_articles is true, limit to exactly 3 items
            if ($showExamples && $perPage === 3) {
                $content = $content->take(3);
            }

            foreach ($content as $item) {
                $contentItem = [
                    'id' => $this->getIdField($item, $type),
                    'title' => $this->getTitleField($item, $type),
                    'summary' => $this->getSummaryField($item, $type),
                    'date' => $this->getDateFieldValue($item, $type),
                    'visitor_count' => $this->getVisitorField($item, $type),
                    'is_new' => $this->getIsNewField($item, $type),
                    'priority' => $this->getPriorityFieldValue($item, $type)
                ];

                // Add type-specific fields
                if ($type === 'articles') {
                    $contentItem['content'] = $item->article_des ?? '';
                    $contentItem['picture'] = $item->article_pic ?? '';
                    $contentItem['publisher_id'] = $item->article_publisher_id ?? '';
                } elseif ($type === 'books') {
                    $contentItem['file'] = $item->book_file ?? '';
                    $contentItem['format'] = $item->book_format ?? '';
                    $contentItem['publisher_id'] = $item->book_publisher_id ?? '';
                } elseif ($type === 'videos') {
                    $contentItem['youtube_id'] = $item->video_youtube_id ?? '';
                    $contentItem['file'] = $item->video_file ?? '';
                } elseif ($type === 'sounds') {
                    $contentItem['file'] = $item->sound_file ?? '';
                } elseif ($type === 'photo_galleries') {
                    $contentItem['picture'] = $item->photo_gallery_pic ?? '';
                } elseif ($type === 'pages') {
                    $contentItem['content'] = $item->pages_content ?? '';
                }

                // Add URLs based on content type
                if ($type === 'books') {
                    $item = $this->addBookUrls($item);
                    $contentItem['book_file_url'] = $item->book_file_url ?? '';
                    $contentItem['book_file_epub_url'] = $item->book_file_epub_url ?? '';
                    $contentItem['book_file_kfx_url'] = $item->book_file_kfx_url ?? '';
                    $contentItem['book_pic_url'] = $item->book_pic_url ?? '';
                } elseif ($type === 'videos') {
                    $item = $this->addVideoUrls($item);
                    $contentItem['video_file_url'] = $item->video_file_url ?? '';
                } elseif ($type === 'sounds') {
                    $item = $this->addSoundUrls($item);
                    $contentItem['sound_file_url'] = $item->sound_file_url ?? '';
                } elseif ($type === 'photo_galleries') {
                    $item = $this->addPhotoGalleryUrls($item);
                    $contentItem['photo_gallery_pic_thumbnail_url'] = $item->photo_gallery_pic_thumbnail_url ?? '';
                    $contentItem['photo_gallery_pic_full_url'] = $item->photo_gallery_pic_full_url ?? '';
                } elseif ($type === 'articles') {
                    $item = $this->addArticleUrls($item);
                    $contentItem['article_file_url'] = $item->article_file_url ?? '';
                }

                $result['content'][] = $contentItem;
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Content retrieved successfully with pagination'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content count for a category
     */
    private function getContentCount($type, $categoryId): int
    {
        $contentModel = $this->getContentModel($type);
        if (!$contentModel) return 0;

        $categoryField = $this->getCategoryField($type);
        return $contentModel::active()->where($categoryField, $categoryId)->count();
    }

    /**
     * Get articles for a category
     */
    private function getCategoryExamples($type, $categoryId, $limit = 3): array
    {
        $contentModel = $this->getContentModel($type);
        if (!$contentModel) return [];

        $categoryField = $this->getCategoryField($type);
        
        // Build query
        // For books, order by date only (oldest first)
        if ($type === 'books') {
            $content = $contentModel::active()
                ->where($categoryField, $categoryId)
                ->orderBy($this->getDateField($type), 'asc')
                ->limit($limit)
                ->get();
        } else {
            $content = $contentModel::active()
                ->where($categoryField, $categoryId)
                ->orderBy($this->getPriorityField($type), 'desc')
                ->orderBy($this->getDateField($type), 'desc')
                ->limit($limit)
                ->get();
        }

        $examples = [];
        foreach ($content as $item) {
            $contentItem = [
                'id' => $this->getIdField($item, $type),
                'title' => $this->getTitleField($item, $type),
                'summary' => $this->getSummaryField($item, $type),
                'date' => $this->getDateFieldValue($item, $type),
                'visitor_count' => $this->getVisitorField($item, $type),
                'is_new' => $this->getIsNewField($item, $type),
                'priority' => $this->getPriorityFieldValue($item, $type)
            ];

            // Add type-specific fields
            if ($type === 'articles') {
                $contentItem['content'] = $item->article_des ?? '';
                $contentItem['picture'] = $item->article_pic ?? '';
                $contentItem['publisher_id'] = $item->article_publisher_id ?? '';
            } elseif ($type === 'books') {
                $contentItem['file'] = $item->book_file ?? '';
                $contentItem['format'] = $item->book_format ?? '';
                $contentItem['publisher_id'] = $item->book_publisher_id ?? '';
            } elseif ($type === 'videos') {
                $contentItem['youtube_id'] = $item->video_youtube_id ?? '';
                $contentItem['file'] = $item->video_file ?? '';
            } elseif ($type === 'sounds') {
                $contentItem['file'] = $item->sound_file ?? '';
            } elseif ($type === 'photo_galleries') {
                $contentItem['picture'] = $item->photo_gallery_pic ?? '';
            } elseif ($type === 'pages') {
                $contentItem['content'] = $item->pages_content ?? '';
            }

            // Add URLs based on content type
            if ($type === 'books') {
                $item = $this->addBookUrls($item);
                $contentItem['book_file_url'] = $item->book_file_url ?? '';
                $contentItem['book_file_epub_url'] = $item->book_file_epub_url ?? '';
                $contentItem['book_file_kfx_url'] = $item->book_file_kfx_url ?? '';
                $contentItem['book_pic_url'] = $item->book_pic_url ?? '';
            } elseif ($type === 'videos') {
                $item = $this->addVideoUrls($item);
                $contentItem['video_file_url'] = $item->video_file_url ?? '';
            } elseif ($type === 'sounds') {
                $item = $this->addSoundUrls($item);
                $contentItem['sound_file_url'] = $item->sound_file_url ?? '';
            } elseif ($type === 'photo_galleries') {
                $item = $this->addPhotoGalleryUrls($item);
                $contentItem['photo_gallery_pic_thumbnail_url'] = $item->photo_gallery_pic_thumbnail_url ?? '';
                $contentItem['photo_gallery_pic_full_url'] = $item->photo_gallery_pic_full_url ?? '';
            } elseif ($type === 'articles') {
                $item = $this->addArticleUrls($item);
                $contentItem['article_file_url'] = $item->article_file_url ?? '';
            }

            $examples[] = $contentItem;
        }

        return $examples;
    }

    /**
     * Get pages for a specific menu
     */
    private function getPagesForMenu($menuId): array
    {
        $pages = Page::active()
            ->where('pages_menus', $menuId)
            ->orderBy('pages_priority', 'desc')
            ->orderBy('pages_date', 'desc')
            ->get();

        $result = [];
        foreach ($pages as $page) {
            $result[] = [
                'id' => $page->pages_id,
                'title' => $page->pages_title,
                'content' => $page->pages_content,
                'language' => $page->pages_lan,
                'visitor_count' => $page->pages_visitor,
                'priority' => $page->pages_priority,
                'date' => $page->pages_date,
                'menu_id' => $page->pages_menus,
                'type' => 'page'
            ];
        }

        return $result;
    }

    /**
     * Get category model by type
     */
    private function getCategoryModel($type)
    {
        $models = [
            'articles' => ArticleCategory::class,
            'books' => BookCategory::class,
            'videos' => VideoCategory::class,
            'sounds' => SoundCategory::class,
            'photo-galleries' => PhotoGalleryCategory::class,
            'photo_galleries' => PhotoGalleryCategory::class
        ];

        return $models[$type] ?? null;
    }

    /**
     * Get content model by type
     */
    private function getContentModel($type)
    {
        $models = [
            'articles' => \App\Models\Article::class,
            'books' => \App\Models\Book::class,
            'videos' => \App\Models\Video::class,
            'sounds' => \App\Models\Sound::class,
            'photo-galleries' => \App\Models\PhotoGallery::class,
            'photo_galleries' => \App\Models\PhotoGallery::class
        ];

        return $models[$type] ?? null;
    }

    /**
     * Get category field name for content model
     */
    private function getCategoryField($type)
    {
        $fields = [
            'articles' => 'article_cat_id',
            'books' => 'book_cat_id',
            'videos' => 'video_cat_id',
            'sounds' => 'sound_cat_id',
            'photo-galleries' => 'photo_gallery_cat_id',
            'photo_galleries' => 'photo_gallery_cat_id'
        ];

        return $fields[$type] ?? null;
    }

    /**
     * Get priority field name for content model
     */
    private function getPriorityField($type)
    {
        $fields = [
            'articles' => 'article_priority',
            'books' => 'book_priority',
            'videos' => 'video_priority',
            'sounds' => 'sound_priority',
            'photo-galleries' => 'photo_gallery_is_new',
            'photo_galleries' => 'photo_gallery_is_new'
        ];

        return $fields[$type] ?? 'priority';
    }

    /**
     * Get date field name for content model
     */
    private function getDateField($type)
    {
        $fields = [
            'articles' => 'article_date',
            'books' => 'book_date',
            'videos' => 'video_date',
            'sounds' => 'sound_date',
            'photo-galleries' => 'photo_gallery_date',
            'photo_galleries' => 'photo_gallery_date'
        ];

        return $fields[$type] ?? 'date';
    }

    /**
     * Get ID field value based on type
     */
    private function getIdField($item, $type)
    {
        $fields = [
            'articles' => 'article_id',
            'books' => 'book_id',
            'videos' => 'video_id',
            'sounds' => 'sound_id',
            'photo-galleries' => 'photo_gallery_id',
            'photo_galleries' => 'photo_gallery_id'
        ];

        $field = $fields[$type] ?? 'id';
        return $item->$field ?? $item->id ?? 0;
    }

    /**
     * Get title field value based on type
     */
    private function getTitleField($item, $type)
    {
        $fields = [
            'articles' => 'article_title',
            'books' => 'book_title',
            'videos' => 'video_title',
            'sounds' => 'sound_title',
            'photo-galleries' => 'photo_gallery_title',
            'photo_galleries' => 'photo_gallery_title'
        ];

        $field = $fields[$type] ?? 'title';
        return $item->$field ?? $item->title ?? '';
    }

    /**
     * Get summary field value based on type
     */
    private function getSummaryField($item, $type)
    {
        $fields = [
            'articles' => 'article_summary',
            'books' => 'book_summary',
            'videos' => 'video_summary',
            'sounds' => 'sound_summary',
            'photo-galleries' => 'photo_gallery_summary',
            'photo_galleries' => 'photo_gallery_summary'
        ];

        $field = $fields[$type] ?? 'summary';
        return $item->$field ?? $item->summary ?? '';
    }

    /**
     * Get date field value based on type
     */
    private function getDateFieldValue($item, $type)
    {
        $fields = [
            'articles' => 'article_date',
            'books' => 'book_date',
            'videos' => 'video_date',
            'sounds' => 'sound_date',
            'photo-galleries' => 'photo_gallery_date',
            'photo_galleries' => 'photo_gallery_date'
        ];

        $field = $fields[$type] ?? 'date';
        return $item->$field ?? $item->date ?? null;
    }

    /**
     * Get visitor field value based on type
     */
    private function getVisitorField($item, $type)
    {
        $fields = [
            'articles' => 'article_visitor',
            'books' => 'book_visitor',
            'videos' => 'video_visitor',
            'sounds' => 'sound_visitor',
            'photo-galleries' => 'photo_gallery_visitor',
            'photo_galleries' => 'photo_gallery_visitor'
        ];

        $field = $fields[$type] ?? 'visitor';
        return $item->$field ?? $item->visitor ?? 0;
    }

    /**
     * Get is_new field value based on type
     */
    private function getIsNewField($item, $type)
    {
        $fields = [
            'articles' => 'article_is_new',
            'books' => 'book_is_new',
            'videos' => 'video_is_new',
            'sounds' => 'sound_is_new',
            'photo-galleries' => 'photo_gallery_is_new',
            'photo_galleries' => 'photo_gallery_is_new'
        ];

        $field = $fields[$type] ?? 'is_new';
        return $item->$field ?? $item->is_new ?? false;
    }

    /**
     * Get priority field value based on type
     */
    private function getPriorityFieldValue($item, $type)
    {
        $fields = [
            'articles' => 'article_priority',
            'books' => 'book_priority',
            'videos' => 'video_priority',
            'sounds' => 'sound_priority',
            'photo-galleries' => 'photo_gallery_is_new',
            'photo_galleries' => 'photo_gallery_is_new'
        ];

        $field = $fields[$type] ?? 'priority';
        return $item->$field ?? $item->priority ?? 0;
    }

    /**
     * Add file and image URLs to book data (same logic as BookController)
     */
    private function addBookUrls($book)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add file URLs
        if ($book->book_file) {
            $book->book_file_url = $baseUrl . '/files/book/' . $book->book_file;
        }
        if ($book->book_file_ePub) {
            $book->book_file_epub_url = $baseUrl . '/files/book/' . $book->book_file_ePub;
        }
        if ($book->book_file_kfx) {
            $book->book_file_kfx_url = $baseUrl . '/files/book/' . $book->book_file_kfx;
        }
        
        // Add image URL
        if ($book->book_pic) {
            $book->book_pic_url = $baseUrl . '/images/book/' . $book->book_pic;
        }
        
        return $book;
    }

    /**
     * Add file URL to video data
     */
    private function addVideoUrls($video)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add file URL
        if ($video->video_file) {
            $video->video_file_url = $baseUrl . '/files/video/' . $video->video_file;
        }
        
        return $video;
    }

    /**
     * Add file URL to sound data
     */
    private function addSoundUrls($sound)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add file URL
        if ($sound->sound_file) {
            $sound->sound_file_url = $baseUrl . '/files/sound/' . $sound->sound_file;
        }
        
        return $sound;
    }

    /**
     * Add image URLs to photo gallery data
     */
    private function addPhotoGalleryUrls($photoGallery)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add thumbnail and full-size image URLs
        if ($photoGallery->photo_gallery_pic) {
            $photoGallery->photo_gallery_pic_thumbnail_url = $baseUrl . '/images/photo_gallery/' . $photoGallery->photo_gallery_pic;
            $photoGallery->photo_gallery_pic_full_url = $baseUrl . '/images/photo_gallery_full_size/' . $photoGallery->photo_gallery_pic;
        }
        
        return $photoGallery;
    }

    /**
     * Add file URL to article data (if file exists)
     */
    private function addArticleUrls($article)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add file URL if exists
        if ($article->article_file) {
            $article->article_file_url = $baseUrl . '/files/article/' . $article->article_file;
        }
        
        return $article;
    }

}
