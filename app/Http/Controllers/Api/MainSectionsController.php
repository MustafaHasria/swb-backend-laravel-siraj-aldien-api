<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Article;
use App\Models\Book;
use App\Models\Video;
use App\Models\Sound;
use App\Models\PhotoGallery;
use Illuminate\Http\JsonResponse;

class MainSectionsController extends Controller
{
    /**
     * Get main sections with hierarchical structure
     */
    public function index(): JsonResponse
    {
        try {
            // Get main menus (bottom_header position)
            $mainMenus = Menu::active()
                ->where('menus_pos', 'bottom_header')
                ->where('menus_active_header', 1)
                ->orderBy('menus_priority', 'asc')
                ->get();

            $sections = [];

            foreach ($mainMenus as $menu) {
                $section = [
                    'id' => $menu->menus_id,
                    'name' => $menu->menus_name,
                    'url' => $menu->menus_url,
                    'priority' => $menu->menus_priority,
                    'language' => $menu->menus_lan,
                    'date' => $menu->menus_date,
                    'subsections' => [],
                    'content_counts' => [
                        'articles' => 0,
                        'books' => 0,
                        'videos' => 0,
                        'sounds' => 0,
                        'photo_galleries' => 0,
                        'pages' => 0
                    ]
                ];

                // Get related pages for this menu
                $pages = Page::active()
                    ->where('pages_title', 'like', '%' . $menu->menus_name . '%')
                    ->orWhere('pages_des', 'like', '%' . $menu->menus_name . '%')
                    ->get();

                foreach ($pages as $page) {
                    $section['subsections'][] = [
                        'type' => 'page',
                        'id' => $page->pages_id,
                        'title' => $page->pages_title,
                        'summary' => $page->pages_summary,
                        'date' => $page->pages_date,
                        'visitor_count' => $page->pages_visitor,
                        'is_new' => $page->pages_is_new,
                        'is_priority' => $page->pages_priority
                    ];
                }

                // Get content counts based on menu name
                $section['content_counts']['articles'] = $this->getContentCount('articles', $menu->menus_name);
                $section['content_counts']['books'] = $this->getContentCount('books', $menu->menus_name);
                $section['content_counts']['videos'] = $this->getContentCount('videos', $menu->menus_name);
                $section['content_counts']['sounds'] = $this->getContentCount('sounds', $menu->menus_name);
                $section['content_counts']['photo_galleries'] = $this->getContentCount('photo_galleries', $menu->menus_name);
                $section['content_counts']['pages'] = $pages->count();

                $sections[] = $section;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'sections' => $sections,
                    'total_sections' => count($sections),
                    'hierarchy_structure' => $this->buildHierarchyStructure($sections)
                ],
                'message' => 'Main sections retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving main sections: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific section with detailed content
     */
    public function show($id): JsonResponse
    {
        try {
            $menu = Menu::active()->find($id);

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section not found'
                ], 404);
            }

            $section = [
                'id' => $menu->menus_id,
                'name' => $menu->menus_name,
                'url' => $menu->menus_url,
                'priority' => $menu->menus_priority,
                'language' => $menu->menus_lan,
                'date' => $menu->menus_date,
                'subsections' => [],
                'content' => [
                    'articles' => [],
                    'books' => [],
                    'videos' => [],
                    'sounds' => [],
                    'photo_galleries' => [],
                    'pages' => []
                ]
            ];

            // Get related pages
            $pages = Page::active()
                ->where('pages_title', 'like', '%' . $menu->menus_name . '%')
                ->orWhere('pages_des', 'like', '%' . $menu->menus_name . '%')
                ->get();

            foreach ($pages as $page) {
                $section['subsections'][] = [
                    'type' => 'page',
                    'id' => $page->pages_id,
                    'title' => $page->pages_title,
                    'summary' => $page->pages_summary,
                    'date' => $page->pages_date,
                    'visitor_count' => $page->pages_visitor
                ];

                $section['content']['pages'][] = [
                    'id' => $page->pages_id,
                    'title' => $page->pages_title,
                    'summary' => $page->pages_summary,
                    'content' => $page->pages_des,
                    'date' => $page->pages_date,
                    'visitor_count' => $page->pages_visitor,
                    'is_new' => $page->pages_is_new,
                    'is_priority' => $page->pages_priority
                ];
            }

            // Get related content based on menu name
            $section['content']['articles'] = $this->getContentByMenu('articles', $menu->menus_name);
            $section['content']['books'] = $this->getContentByMenu('books', $menu->menus_name);
            $section['content']['videos'] = $this->getContentByMenu('videos', $menu->menus_name);
            $section['content']['sounds'] = $this->getContentByMenu('sounds', $menu->menus_name);
            $section['content']['photo_galleries'] = $this->getContentByMenu('photo_galleries', $menu->menus_name);

            return response()->json([
                'success' => true,
                'data' => $section,
                'message' => 'Section details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content count by menu name
     */
    private function getContentCount($type, $menuName): int
    {
        $model = $this->getModelByType($type);
        if (!$model) return 0;

        return $model::active()
            ->where(function($query) use ($menuName) {
                $query->where('title', 'like', '%' . $menuName . '%')
                      ->orWhere('summary', 'like', '%' . $menuName . '%')
                      ->orWhere('des', 'like', '%' . $menuName . '%');
            })
            ->count();
    }

    /**
     * Get content by menu name
     */
    private function getContentByMenu($type, $menuName): array
    {
        $model = $this->getModelByType($type);
        if (!$model) return [];

        try {
            $content = $model::active()
                ->where(function($query) use ($menuName) {
                    $query->where('title', 'like', '%' . $menuName . '%')
                          ->orWhere('summary', 'like', '%' . $menuName . '%')
                          ->orWhere('des', 'like', '%' . $menuName . '%');
                })
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();

            return $content->map(function($item) {
                return [
                    'id' => $item->id ?? $item->article_id ?? $item->book_id ?? $item->video_id ?? $item->sound_id ?? $item->photo_gallery_id ?? $item->pages_id,
                    'title' => $item->title ?? $item->article_title ?? $item->book_title ?? $item->video_title ?? $item->sound_title ?? $item->photo_gallery_title ?? $item->pages_title,
                    'summary' => $item->summary ?? $item->article_summary ?? $item->book_summary ?? $item->video_summary ?? $item->sound_summary ?? $item->photo_gallery_summary ?? $item->pages_summary,
                    'date' => $item->date ?? $item->article_date ?? $item->book_date ?? $item->video_date ?? $item->sound_date ?? $item->photo_gallery_date ?? $item->pages_date,
                    'visitor_count' => $item->visitor ?? $item->article_visitor ?? $item->book_visitor ?? $item->video_visitor ?? $item->sound_visitor ?? $item->photo_gallery_visitor ?? $item->pages_visitor
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get model by type
     */
    private function getModelByType($type)
    {
        $models = [
            'articles' => Article::class,
            'books' => Book::class,
            'videos' => Video::class,
            'sounds' => Sound::class,
            'photo_galleries' => PhotoGallery::class
        ];

        return $models[$type] ?? null;
    }

    /**
     * Build hierarchy structure
     */
    private function buildHierarchyStructure($sections): array
    {
        return [
            'main_sections' => $sections,
            'structure' => [
                'level_1' => 'Main Menus (bottom_header)',
                'level_2' => 'Related Pages',
                'level_3' => 'Content Items (Articles, Books, Videos, etc.)'
            ],
            'relationships' => [
                'menu_to_pages' => 'Pages related to menu name',
                'menu_to_content' => 'Content items related to menu name',
                'hierarchical_display' => 'Menu -> Pages -> Content'
            ]
        ];
    }
}
