<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class TestController extends Controller
{
    /**
     * Simple test endpoint
     */
    public function test(): JsonResponse
    {
        try {
            // Get menus
            $menus = Menu::active()
                ->where('menus_pos', 'bottom_header')
                ->orderBy('menus_priority', 'asc')
                ->get();

            // Get pages
            $pages = Page::active()
                ->orderBy('pages_date', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'menus' => $menus,
                    'pages' => $pages,
                    'menus_count' => $menus->count(),
                    'pages_count' => $pages->count()
                ],
                'message' => 'Test data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
}




