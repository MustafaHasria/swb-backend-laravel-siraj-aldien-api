<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * Get all active books
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::with(['category', 'captions', 'votes'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('book_cat_id', $request->category_id);
        }

        // Filter by new books
        if ($request->has('is_new') && $request->is_new) {
            $query->new();
        }

        // Filter by priority books
        if ($request->has('is_priority') && $request->is_priority) {
            $query->priority();
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('book_title', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $books = $query->orderBy('book_date', 'desc')
            ->paginate($perPage);

        // Add file and image URLs to each book
        $books->getCollection()->transform(function ($book) {
            return $this->addBookUrls($book);
        });

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => 'Books retrieved successfully'
        ]);
    }

    /**
     * Get book by ID
     */
    public function show($id): JsonResponse
    {
        $book = Book::with(['category', 'captions', 'votes'])
            ->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found'
            ], 404);
        }

        // Increment visitor count
        $book->increment('book_visitor');

        // Add file and image URLs
        $book = $this->addBookUrls($book);

        return response()->json([
            'success' => true,
            'data' => $book,
            'message' => 'Book retrieved successfully'
        ]);
    }

    /**
     * Get new books
     */
    public function new(): JsonResponse
    {
        $books = Book::with(['category'])
            ->active()
            ->new()
            ->orderBy('book_date', 'desc')
            ->limit(10)
            ->get();

        // Add file and image URLs to each book
        $books->transform(function ($book) {
            return $this->addBookUrls($book);
        });

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => 'New books retrieved successfully'
        ]);
    }

    /**
     * Get priority books
     */
    public function priority(): JsonResponse
    {
        $books = Book::with(['category'])
            ->active()
            ->priority()
            ->orderBy('book_date', 'desc')
            ->get();

        // Add file and image URLs to each book
        $books->transform(function ($book) {
            return $this->addBookUrls($book);
        });

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => 'Priority books retrieved successfully'
        ]);
    }

    /**
     * Get books by category
     */
    public function byCategory($categoryId, Request $request): JsonResponse
    {
        $query = Book::with(['category', 'captions', 'votes'])
            ->active()
            ->where('book_cat_id', $categoryId);

        // Search within category
        if ($request->has('search')) {
            $query->where('book_title', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $books = $query->orderBy('book_date', 'desc')
            ->paginate($perPage);

        // Add file and image URLs to each book
        $books->getCollection()->transform(function ($book) {
            return $this->addBookUrls($book);
        });

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => 'Books by category retrieved successfully'
        ]);
    }

    /**
     * Get books with specific file format
     */
    public function byFormat($format): JsonResponse
    {
        $query = Book::with(['category'])
            ->active();

        switch ($format) {
            case 'pdf':
                $query->whereNotNull('book_file');
                break;
            case 'epub':
                $query->whereNotNull('book_file_ePub');
                break;
            case 'kfx':
                $query->whereNotNull('book_file_kfx');
                break;
        }

        $books = $query->orderBy('book_date', 'desc')->get();

        // Add file and image URLs to each book
        $books->transform(function ($book) {
            return $this->addBookUrls($book);
        });

        return response()->json([
            'success' => true,
            'data' => $books,
            'message' => "Books with {$format} format retrieved successfully"
        ]);
    }

    /**
     * Add file and image URLs to book data
     */
    private function addBookUrls($book)
    {
        $baseUrl = 'https://srajalden.com';
        
        // Add file URLs
        if ($book->book_file) {
            $book->book_file_url = $baseUrl . '/files/book/' . $book->book_file;
        }
        if ($book->book_file_ePub) {
            $book->book_file_epub_url = $baseUrl . '/files/book/' . $book->book_file_ePub;
        }
        if ($book->book_file_kfx) {
            $book->book_file_kfx_url = $baseUrl . '/files/book/' . $book->book_file_kfx;
        }
        
        // Add image URL
        if ($book->book_pic) {
            $book->book_pic_url = $baseUrl . '/images/book/' . $book->book_pic;
        }
        
        return $book;
    }
}








