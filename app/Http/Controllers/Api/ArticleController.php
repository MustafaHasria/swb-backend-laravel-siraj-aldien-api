<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * Get all active articles
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'captions', 'votes'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('article_cat_id', $request->category_id);
        }

        // Filter by new articles
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority articles
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('article_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $articles = $query->orderBy('article_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles,
            'message' => 'Articles retrieved successfully'
        ]);
    }

    /**
     * Get article by ID
     */
    public function show($id): JsonResponse
    {
        $article = Article::with(['category', 'captions', 'votes'])
            ->find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        // Increment visitor count
        $article->increment('article_visitor');

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article retrieved successfully'
        ]);
    }

    /**
     * Get new articles
     */
    public function new(): JsonResponse
    {
        $articles = Article::with(['category'])
            ->active()
            ->new()
            ->orderBy('article_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
            'message' => 'New articles retrieved successfully'
        ]);
    }

    /**
     * Get priority articles
     */
    public function priority(): JsonResponse
    {
        $articles = Article::with(['category'])
            ->active()
            ->priority()
            ->orderBy('article_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $articles,
            'message' => 'Priority articles retrieved successfully'
        ]);
    }

    /**
     * Get articles by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        $query = Article::with(['category', 'captions', 'votes'])
            ->active()
            ->where('article_cat_id', $categoryId);

        // Search within category
        if ($request->has('search')) {
            $query->where('article_title', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $articles = $query->orderBy('article_date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles,
            'message' => 'Articles by category retrieved successfully'
        ]);
    }
}




