<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VideoController extends Controller
{
    /**
     * Get all active videos
     */
    public function index(Request $request): JsonResponse
    {
        $query = Video::with(['category', 'captions', 'votes'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('video_cat_id', $request->category_id);
        }

        // Filter by new videos
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority videos
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('video_title', 'like', '%' . $request->search . '%');
        }

        // Filter by YouTube videos
        if ($request->has('youtube_only') && $request->youtube_only) {
            $query->whereNotNull('video_youtube_id');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $videos = $query->orderBy('video_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'Videos retrieved successfully'
        ]);
    }

    /**
     * Get video by ID
     */
    public function show($id): JsonResponse
    {
        $video = Video::with(['category', 'captions', 'votes'])
            ->find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404);
        }

        // Increment visitor count
        $video->increment('video_visitor');

        return response()->json([
            'success' => true,
            'data' => $video,
            'message' => 'Video retrieved successfully'
        ]);
    }

    /**
     * Get new videos
     */
    public function new(): JsonResponse
    {
        $videos = Video::with(['category'])
            ->active()
            ->new()
            ->orderBy('video_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'New videos retrieved successfully'
        ]);
    }

    /**
     * Get priority videos
     */
    public function priority(): JsonResponse
    {
        $videos = Video::with(['category'])
            ->active()
            ->priority()
            ->orderBy('video_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'Priority videos retrieved successfully'
        ]);
    }

    /**
     * Get videos by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        $query = Video::with(['category', 'captions', 'votes'])
            ->active()
            ->where('video_cat_id', $categoryId);

        // Search within category
        if ($request->has('search')) {
            $query->where('video_title', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $videos = $query->orderBy('video_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'Videos by category retrieved successfully'
        ]);
    }

    /**
     * Get YouTube videos
     */
    public function youtube(): JsonResponse
    {
        $videos = Video::with(['category'])
            ->active()
            ->whereNotNull('video_youtube_id')
            ->orderBy('video_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $videos,
            'message' => 'YouTube videos retrieved successfully'
        ]);
    }
}




