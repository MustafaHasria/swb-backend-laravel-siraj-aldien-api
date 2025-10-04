<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlockController extends Controller
{
    /**
     * Get all active blocks
     */
    public function index(Request $request): JsonResponse
    {
        $query = Block::active();

        // Filter by new blocks
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority blocks
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('blocks_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $blocks = $query->orderBy('blocks_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $blocks,
            'message' => 'Blocks retrieved successfully'
        ]);
    }

    /**
     * Get block by ID
     */
    public function show($id): JsonResponse
    {
        $block = Block::find($id);

        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'Block not found'
            ], 404);
        }

        // Increment visitor count
        $block->increment('blocks_visitor');

        return response()->json([
            'success' => true,
            'data' => $block,
            'message' => 'Block retrieved successfully'
        ]);
    }

    /**
     * Get new blocks
     */
    public function new(): JsonResponse
    {
        $blocks = Block::active()
            ->new()
            ->orderBy('blocks_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blocks,
            'message' => 'New blocks retrieved successfully'
        ]);
    }

    /**
     * Get priority blocks
     */
    public function priority(): JsonResponse
    {
        $blocks = Block::active()
            ->priority()
            ->orderBy('blocks_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blocks,
            'message' => 'Priority blocks retrieved successfully'
        ]);
    }
}




