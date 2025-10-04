<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LinkController extends Controller
{
    /**
     * Get all active links
     */
    public function index(Request $request): JsonResponse
    {
        $query = Link::with('menu')->active();

        // Filter by menu
        if ($request->has('menu_id')) {
            $query->where('links_menus', $request->menu_id);
        }

        // Filter by language
        if ($request->has('language')) {
            $query->where('links_lan', $request->language);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('links_title', 'like', '%' . $request->search . '%');
        }

        // Order by priority
        $links = $query->orderBy('links_priority', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $links,
            'message' => 'Links retrieved successfully'
        ]);
    }

    /**
     * Get link by ID
     */
    public function show($id): JsonResponse
    {
        $link = Link::with('menu')->find($id);

        if (!$link) {
            return response()->json([
                'success' => false,
                'message' => 'Link not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $link,
            'message' => 'Link retrieved successfully'
        ]);
    }

    /**
     * Get links by menu
     */
    public function byMenu($menuId): JsonResponse
    {
        $links = Link::with('menu')
            ->active()
            ->where('links_menus', $menuId)
            ->orderBy('links_priority', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $links,
            'message' => 'Links by menu retrieved successfully'
        ]);
    }
}





