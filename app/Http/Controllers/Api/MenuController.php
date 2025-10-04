<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    /**
     * Get all active menus
     */
    public function index(Request $request): JsonResponse
    {
        $query = Menu::active();

        // Filter by position
        if ($request->has('position')) {
            $query->position($request->position);
        }

        // Filter by language
        if ($request->has('language')) {
            $query->where('menus_lan', $request->language);
        }

        // Order by priority
        $query->orderBy('menus_priority', 'asc');

        $menus = $query->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
            'message' => 'Menus retrieved successfully'
        ]);
    }

    /**
     * Get menu by ID
     */
    public function show($id): JsonResponse
    {
        $menu = Menu::with('links')->find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $menu,
            'message' => 'Menu retrieved successfully'
        ]);
    }

    /**
     * Get menus by position
     */
    public function byPosition($position): JsonResponse
    {
        $menus = Menu::active()
            ->position($position)
            ->orderBy('menus_priority', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
            'message' => 'Menus by position retrieved successfully'
        ]);
    }

    /**
     * Get header menus
     */
    public function header(): JsonResponse
    {
        $menus = Menu::active()
            ->header()
            ->orderBy('menus_priority', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $menus,
            'message' => 'Header menus retrieved successfully'
        ]);
    }
}








