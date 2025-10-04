<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sound;
use App\Models\SoundCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SoundController extends Controller
{
    /**
     * Get all active sounds
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sound::with(['category', 'captions', 'votes'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('sound_cat_id', $request->category_id);
        }

        // Filter by new sounds
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority sounds
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('sound_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $sounds = $query->orderBy('sound_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sounds,
            'message' => 'Sounds retrieved successfully'
        ]);
    }

    /**
     * Get sound by ID
     */
    public function show($id): JsonResponse
    {
        $sound = Sound::with(['category', 'captions', 'votes'])
            ->find($id);

        if (!$sound) {
            return response()->json([
                'success' => false,
                'message' => 'Sound not found'
            ], 404);
        }

        // Increment visitor count
        $sound->increment('sound_visitor');

        return response()->json([
            'success' => true,
            'data' => $sound,
            'message' => 'Sound retrieved successfully'
        ]);
    }

    /**
     * Get new sounds
     */
    public function new(): JsonResponse
    {
        $sounds = Sound::with(['category'])
            ->active()
            ->new()
            ->orderBy('sound_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sounds,
            'message' => 'New sounds retrieved successfully'
        ]);
    }

    /**
     * Get priority sounds
     */
    public function priority(): JsonResponse
    {
        $sounds = Sound::with(['category'])
            ->active()
            ->priority()
            ->orderBy('sound_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sounds,
            'message' => 'Priority sounds retrieved successfully'
        ]);
    }

    /**
     * Get sounds by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        $query = Sound::with(['category', 'captions', 'votes'])
            ->active()
            ->where('sound_cat_id', $categoryId);

        // Search within category
        if ($request->has('search')) {
            $query->where('sound_title', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $sounds = $query->orderBy('sound_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sounds,
            'message' => 'Sounds by category retrieved successfully'
        ]);
    }
}





