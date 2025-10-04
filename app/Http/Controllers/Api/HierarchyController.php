<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class HierarchyController extends Controller
{
    /**
     * Get hierarchical sections (Menu-Page relationship)
     */
    public function index(): JsonResponse
    {
        try {
            $menus = Menu::active()
                ->where('menus_pos', 'bottom_header')
                ->where('menus_active_header', 1)
                ->orderBy('menus_priority', 'asc')
                ->get();

            $hierarchy = [];
            foreach ($menus as $menu) {
                $section = [
                    'id' => $menu->menus_id,
                    'name' => $menu->menus_name,
                    'url' => $menu->menus_url,
                    'priority' => $menu->menus_priority,
                    'language' => $menu->menus_lan,
                    'date' => $menu->menus_date,
                    'subsections' => [],
                    'content_counts' => [
                        'pages' => 0,
                        'articles' => 0,
                        'books' => 0,
                        'videos' => 0,
                        'sounds' => 0,
                        'photo_galleries' => 0
                    ]
                ];

                // Get related pages
                $pages = Page::active()
                    ->where('pages_title', 'like', '%' . $menu->menus_name . '%')
                    ->orWhere('pages_content', 'like', '%' . $menu->menus_name . '%')
                    ->limit(5)
                    ->get();

                foreach ($pages as $page) {
                    $section['subsections'][] = [
                        'type' => 'page',
                        'id' => $page->pages_id,
                        'title' => $page->pages_title,
                        'content' => $page->pages_content,
                        'date' => $page->pages_date,
                        'visitor_count' => $page->pages_visitor,
                        'priority' => $page->pages_priority,
                        'language' => $page->pages_lan
                    ];
                }

                $section['content_counts']['pages'] = $pages->count();

                $hierarchy[] = $section;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'hierarchy' => $hierarchy,
                    'total_sections' => count($hierarchy),
                    'structure_info' => [
                        'level_1' => 'Main Menus (bottom_header)',
                        'level_2' => 'Related Pages',
                        'description' => 'Hierarchical structure showing menu-page relationships'
                    ]
                ],
                'message' => 'Hierarchical sections retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get simple sections (menus only)
     */
    public function simple(): JsonResponse
    {
        try {
            $menus = Menu::active()
                ->where('menus_pos', 'bottom_header')
                ->where('menus_active_header', 1)
                ->orderBy('menus_priority', 'asc')
                ->get();

            $sections = [];
            foreach ($menus as $menu) {
                $sections[] = [
                    'id' => $menu->menus_id,
                    'name' => $menu->menus_name,
                    'url' => $menu->menus_url,
                    'priority' => $menu->menus_priority,
                    'language' => $menu->menus_lan,
                    'date' => $menu->menus_date
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'sections' => $sections,
                    'total_sections' => count($sections)
                ],
                'message' => 'Main sections retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}





