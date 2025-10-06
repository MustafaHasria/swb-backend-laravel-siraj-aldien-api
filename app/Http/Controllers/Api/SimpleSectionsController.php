<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class SimpleSectionsController extends Controller
{
    /**
     * Get main sections with simple structure
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
                    'subsections' => []
                ];

                // Get related pages for this menu
                $pages = Page::active()
                    ->where('pages_title', 'like', '%' . $menu->menus_name . '%')
                    ->orWhere('pages_des', 'like', '%' . $menu->menus_name . '%')
                    ->limit(5)
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

                $sections[] = $section;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'sections' => $sections,
                    'total_sections' => count($sections),
                    'hierarchy_info' => [
                        'level_1' => 'Main Menus (bottom_header)',
                        'level_2' => 'Related Pages',
                        'description' => 'Each section contains its related pages based on name matching'
                    ]
                ],
                'message' => 'Main sections retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving main sections: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
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
                'pages' => []
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

                $section['pages'][] = [
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
}
