<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoCategoryController extends Controller
{
    /**
     * Get all active categories
     */
    public function index(Request $request): JsonResponse
    {
        $query = VideoCategory::active();

        // Filter by language
        if ($request->has('language')) {
            $query->where('cat_lan', $request->language);
        }

        // Filter by menu
        if ($request->has('menu_id')) {
            $query->where('cat_menus', $request->menu_id);
        }

        // Get main categories only
        if ($request->has('main_only') && $request->main_only) {
            $query->main();
        }

        $categories = $query->orderBy('cat_pos', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Video categories retrieved successfully'
        ]);
    }

    /**
     * Get category by ID
     */
    public function show($id): JsonResponse
    {
        $category = VideoCategory::with(['parent', 'children', 'videos', 'menu'])
            ->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category retrieved successfully'
        ]);
    }

    /**
     * Get main categories (no parent)
     */
    public function main(): JsonResponse
    {
        $categories = VideoCategory::active()
            ->main()
            ->with(['children'])
            ->orderBy('cat_pos', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Main categories retrieved successfully'
        ]);
    }

    /**
     * Get subcategories by parent ID
     */
    public function subcategories($parentId): JsonResponse
    {
        $categories = VideoCategory::active()
            ->where('cat_father_id', $parentId)
            ->with(['children'])
            ->orderBy('cat_pos', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Subcategories retrieved successfully'
        ]);
    }

    /**
     * Get category hierarchy
     */
    public function hierarchy(): JsonResponse
    {
        $categories = VideoCategory::active()
            ->main()
            ->with(['children' => function($query) {
                $query->active()->with(['children']);
            }])
            ->orderBy('cat_pos', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
            'message' => 'Category hierarchy retrieved successfully'
        ]);
    }
}





