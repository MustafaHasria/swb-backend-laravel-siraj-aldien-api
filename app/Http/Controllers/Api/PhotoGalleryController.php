<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhotoGallery;
use App\Models\PhotoGalleryCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PhotoGalleryController extends Controller
{
    /**
     * Get all active photo galleries
     */
    public function index(Request $request): JsonResponse
    {
        $query = PhotoGallery::with(['category', 'captions', 'votes'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('gallery_cat_id', $request->category_id);
        }

        // Filter by new galleries
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority galleries
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('gallery_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $galleries = $query->orderBy('gallery_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $galleries,
            'message' => 'Photo galleries retrieved successfully'
        ]);
    }

    /**
     * Get photo gallery by ID
     */
    public function show($id): JsonResponse
    {
        $gallery = PhotoGallery::with(['category', 'captions', 'votes'])
            ->find($id);

        if (!$gallery) {
            return response()->json([
                'success' => false,
                'message' => 'Photo gallery not found'
            ], 404);
        }

        // Increment visitor count
        $gallery->increment('gallery_visitor');

        return response()->json([
            'success' => true,
            'data' => $gallery,
            'message' => 'Photo gallery retrieved successfully'
        ]);
    }

    /**
     * Get new photo galleries
     */
    public function new(): JsonResponse
    {
        $galleries = PhotoGallery::with(['category'])
            ->active()
            ->new()
            ->orderBy('gallery_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $galleries,
            'message' => 'New photo galleries retrieved successfully'
        ]);
    }

    /**
     * Get priority photo galleries
     */
    public function priority(): JsonResponse
    {
        $galleries = PhotoGallery::with(['category'])
            ->active()
            ->priority()
            ->orderBy('gallery_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $galleries,
            'message' => 'Priority photo galleries retrieved successfully'
        ]);
    }

    /**
     * Get photo galleries by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        $query = PhotoGallery::with(['category', 'captions', 'votes'])
            ->active()
            ->where('gallery_cat_id', $categoryId);

        // Search within category
        if ($request->has('search')) {
            $query->where('gallery_title', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $galleries = $query->orderBy('gallery_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $galleries,
            'message' => 'Photo galleries by category retrieved successfully'
        ]);
    }
}





