<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Get all active pages (titles only)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Page::active()->select('pages_id', 'pages_title', 'pages_menus', 'pages_priority', 'pages_date')
            ->where('pages_menus', 46); // Only get pages with menu_id = 46

        // Filter by new pages
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority pages
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('pages_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pages = $query->orderBy('pages_priority', 'desc')
            ->orderBy('pages_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pages,
            'message' => 'Pages titles retrieved successfully'
        ]);
    }

    /**
     * Get page by ID
     */
    public function show($id): JsonResponse
    {
        $page = Page::find($id);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        // Increment visitor count
        $page->increment('pages_visitor');

        return response()->json([
            'success' => true,
            'data' => $page,
            'message' => 'Page retrieved successfully'
        ]);
    }

    /**
     * Get new pages
     */
    public function new(): JsonResponse
    {
        $pages = Page::active()
            ->new()
            ->orderBy('pages_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages,
            'message' => 'New pages retrieved successfully'
        ]);
    }

    /**
     * Get priority pages
     */
    public function priority(): JsonResponse
    {
        $pages = Page::active()
            ->priority()
            ->orderBy('pages_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pages,
            'message' => 'Priority pages retrieved successfully'
        ]);
    }
}


