<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Book;
use App\Models\Video;
use App\Models\Sound;
use App\Models\PhotoGallery;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Global search across all content types
     * GET /api/search?q=keyword&type=all&sort=relevance&filters=priority,new
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q', '');
            $searchType = $request->get('type', 'all'); // all, articles, books, videos, sounds, photo_galleries, pages
            $sortBy = $request->get('sort', 'relevance'); // relevance, date, popularity, priority
            $filters = $request->get('filters', ''); // priority, new, popular
            $perPage = $request->get('per_page', 15);
            
            if (empty($keyword)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search keyword is required. Use ?q=keyword'
                ], 400);
            }

            // Clean and prepare search terms
            $searchTerms = $this->prepareSearchTerms($keyword);
            
            $results = [];

            // Search in Articles
            if ($searchType === 'all' || $searchType === 'articles') {
                $articles = $this->searchArticles($searchTerms, $sortBy, $filters, $perPage);
                $results['articles'] = [
                    'data' => $articles,
                    'count' => $articles->count(),
                    'label' => 'المقالات'
                ];
            }

            // Search in Books
            if ($searchType === 'all' || $searchType === 'books') {
                $books = $this->searchBooks($searchTerms, $sortBy, $filters, $perPage);
                $results['books'] = [
                    'data' => $books,
                    'count' => $books->count(),
                    'label' => 'الكتب'
                ];
            }

            // Search in Videos
            if ($searchType === 'all' || $searchType === 'videos') {
                $videos = $this->searchVideos($searchTerms, $sortBy, $filters, $perPage);
                $results['videos'] = [
                    'data' => $videos,
                    'count' => $videos->count(),
                    'label' => 'الفيديوهات'
                ];
            }

            // Search in Sounds
            if ($searchType === 'all' || $searchType === 'sounds') {
                $sounds = $this->searchSounds($searchTerms, $sortBy, $filters, $perPage);
                $results['sounds'] = [
                    'data' => $sounds,
                    'count' => $sounds->count(),
                    'label' => 'الأصوات'
                ];
            }

            // Search in Photo Galleries
            if ($searchType === 'all' || $searchType === 'photo_galleries') {
                $photoGalleries = $this->searchPhotoGalleries($searchTerms, $sortBy, $filters, $perPage);
                $results['photo_galleries'] = [
                    'data' => $photoGalleries,
                    'count' => $photoGalleries->count(),
                    'label' => 'معارض الصور'
                ];
            }

            // Search in Pages
            if ($searchType === 'all' || $searchType === 'pages') {
                $pages = $this->searchPages($searchTerms, $sortBy, $filters, $perPage);
                $results['pages'] = [
                    'data' => $pages,
                    'count' => $pages->count(),
                    'label' => 'الصفحات'
                ];
            }

            $totalResults = array_sum(array_map(function($result) {
                return $result['count'];
            }, $results));

            return response()->json([
                'success' => true,
                'data' => [
                    'keyword' => $keyword,
                    'search_type' => $searchType,
                    'sort_by' => $sortBy,
                    'filters' => $filters,
                    'total_results' => $totalResults,
                    'results' => $results
                ],
                'message' => 'Search completed successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search error: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Get search suggestions based on existing content
     * GET /api/search/suggestions?q=keyword
     */
    public function suggestions(Request $request): JsonResponse
    {
        try {
            $keyword = $request->get('q', '');
            
            if (strlen($keyword) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Keyword too short'
                ]);
            }

            $suggestions = [];

            // Get suggestions from titles
            $titleSuggestions = Article::active()
                ->where('article_title', 'like', '%' . $keyword . '%')
                ->limit(5)
                ->pluck('article_title')
                ->toArray();

            $suggestions = array_merge($suggestions, $titleSuggestions);

            // Get suggestions from book titles
            $bookSuggestions = Book::active()
                ->where('book_title', 'like', '%' . $keyword . '%')
                ->limit(5)
                ->pluck('book_title')
                ->toArray();

            $suggestions = array_merge($suggestions, $bookSuggestions);

            // Remove duplicates and limit
            $suggestions = array_unique($suggestions);
            $suggestions = array_slice($suggestions, 0, 10);

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'Suggestions retrieved successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare search terms by cleaning and processing the keyword
     */
    private function prepareSearchTerms($keyword)
    {
        $keyword = trim($keyword);
        
        // Remove common Arabic stop words
        $stopWords = ['في', 'من', 'إلى', 'على', 'هذا', 'هذه', 'التي', 'الذي', 'التي', 'والتي', 'والذي'];
        $words = explode(' ', $keyword);
        $filteredWords = array_filter($words, function($word) use ($stopWords) {
            return !in_array(trim($word), $stopWords);
        });

        return [
            'original' => $keyword,
            'exact' => $keyword,
            'words' => array_values($filteredWords)
        ];
    }

    /**
     * Search in articles
     */
    private function searchArticles($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = Article::active()
            ->with('category');

        $this->buildSearchConditions($query, $searchTerms, [
            'article_title' => 10, // Highest weight
            'article_summary' => 5,
            'article_des' => 3
        ]);

        $this->applyFilters($query, $filters, 'article');
        $this->applySorting($query, $sortBy, 'article');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatArticleResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'summary' => 5,
                'description' => 3
            ]);
            return $item;
        });
    }

    /**
     * Search in books
     */
    private function searchBooks($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = Book::active()
            ->with('category');

        $this->buildSearchConditions($query, $searchTerms, [
            'book_title' => 10,
            'book_summary' => 5,
            'book_des' => 3
        ]);

        $this->applyFilters($query, $filters, 'book');
        $this->applySorting($query, $sortBy, 'book');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatBookResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'summary' => 5,
                'description' => 3
            ]);
            return $item;
        });
    }

    /**
     * Search in videos
     */
    private function searchVideos($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = Video::active()
            ->with('category');

        $this->buildSearchConditions($query, $searchTerms, [
            'video_title' => 10,
            'video_summary' => 5,
            'video_des' => 3
        ]);

        $this->applyFilters($query, $filters, 'video');
        $this->applySorting($query, $sortBy, 'video');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatVideoResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'summary' => 5,
                'description' => 3
            ]);
            return $item;
        });
    }

    /**
     * Search in sounds
     */
    private function searchSounds($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = Sound::active()
            ->with('category');

        $this->buildSearchConditions($query, $searchTerms, [
            'sound_title' => 10,
            'sound_summary' => 5,
            'sound_des' => 3
        ]);

        $this->applyFilters($query, $filters, 'sound');
        $this->applySorting($query, $sortBy, 'sound');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatSoundResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'summary' => 5,
                'description' => 3
            ]);
            return $item;
        });
    }

    /**
     * Search in photo galleries
     */
    private function searchPhotoGalleries($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = PhotoGallery::active()
            ->with('category');

        $this->buildSearchConditions($query, $searchTerms, [
            'photo_gallery_title' => 10,
            'photo_gallery_summary' => 5
        ]);

        $this->applyFilters($query, $filters, 'photo_gallery');
        $this->applySorting($query, $sortBy, 'photo_gallery');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatPhotoGalleryResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'summary' => 5
            ]);
            return $item;
        });
    }

    /**
     * Search in pages
     */
    private function searchPages($searchTerms, $sortBy, $filters, $perPage)
    {
        $query = Page::active();

        $this->buildSearchConditions($query, $searchTerms, [
            'pages_title' => 10,
            'pages_content' => 3
        ]);

        $this->applyFilters($query, $filters, 'pages');
        $this->applySorting($query, $sortBy, 'pages');

        return $query->limit($perPage)->get()->map(function($item) use ($searchTerms) {
            $item = $this->formatPageResult($item);
            $item['relevance_score'] = $this->calculateRelevanceScore($item, $searchTerms, [
                'title' => 10,
                'content' => 3
            ]);
            return $item;
        });
    }

    /**
     * Build search conditions with multiple matching strategies
     */
    private function buildSearchConditions($query, $searchTerms, $fields)
    {
        $query->where(function($q) use ($searchTerms, $fields) {
            // Exact phrase match
            $q->where(function($subQuery) use ($searchTerms, $fields) {
                foreach ($fields as $field => $weight) {
                    $subQuery->orWhere($field, 'like', '%' . $searchTerms['exact'] . '%');
                }
            });

            // Individual word matches
            if (!empty($searchTerms['words'])) {
                $q->orWhere(function($subQuery) use ($searchTerms, $fields) {
                    foreach ($searchTerms['words'] as $word) {
                        $subQuery->where(function($wordQuery) use ($word, $fields) {
                            foreach ($fields as $field => $weight) {
                                $wordQuery->orWhere($field, 'like', '%' . $word . '%');
                            }
                        });
                    }
                });
            }
        });
    }

    /**
     * Calculate relevance score manually
     */
    private function calculateRelevanceScore($item, $searchTerms, $fieldWeights)
    {
        $score = 0;
        
        // Check each field for matches
        foreach ($fieldWeights as $field => $weight) {
            $fieldValue = $item[$field] ?? '';
            
            // Check for word matches
            foreach ($searchTerms['words'] as $word) {
                if (stripos($fieldValue, $word) !== false) {
                    $score += $weight;
                }
            }
            
            // Check for exact phrase match (double points)
            if (stripos($fieldValue, $searchTerms['exact']) !== false) {
                $score += $weight * 2;
            }
        }
        
        return $score;
    }

    /**
     * Apply filters based on content type
     */
    private function applyFilters($query, $filters, $type)
    {
        if (empty($filters)) {
            return;
        }

        $filterArray = explode(',', $filters);
        foreach ($filterArray as $filter) {
            $filter = trim($filter);
            switch ($filter) {
                case 'priority':
                    $query->where($type . '_priority', '>', 0);
                    break;
                case 'new':
                    $query->where($type . '_is_new', 1);
                    break;
                case 'popular':
                    $query->where($type . '_visitor', '>', 10);
                    break;
            }
        }
    }

    /**
     * Apply sorting based on user preference
     */
    private function applySorting($query, $sortBy, $type)
    {
        switch ($sortBy) {
            case 'date':
                $query->orderBy($type . '_date', 'desc');
                break;
            case 'popularity':
                if (in_array($type, ['photo_gallery', 'pages'])) {
                    // Photo galleries and pages don't have visitor column
                    $query->orderBy($type . '_date', 'desc');
                } else {
                    $query->orderBy($type . '_visitor', 'desc');
                }
                break;
            case 'priority':
                if (in_array($type, ['photo_gallery'])) {
                    // Photo galleries don't have priority column
                    $query->orderBy($type . '_date', 'desc');
                } else {
                    $query->orderBy($type . '_priority', 'desc')
                          ->orderBy($type . '_date', 'desc');
                }
                break;
            case 'relevance':
            default:
                if (in_array($type, ['photo_gallery'])) {
                    // Photo galleries don't have priority column
                    $query->orderBy($type . '_date', 'desc');
                } else {
                    $query->orderBy($type . '_priority', 'desc')
                          ->orderBy($type . '_date', 'desc');
                }
                break;
        }
    }

    /**
     * Format article result
     */
    private function formatArticleResult($item)
    {
        return [
            'id' => $item->article_id,
            'title' => $item->article_title,
            'summary' => $item->article_summary,
            'description' => $item->article_des,
            'picture' => $item->article_pic,
            'visitor_count' => $item->article_visitor,
            'is_new' => $item->article_is_new,
            'priority' => $item->article_priority,
            'date' => $item->article_date,
            'category' => $item->category,
            'type' => 'article',
            'type_label' => 'مقال'
        ];
    }

    /**
     * Format book result
     */
    private function formatBookResult($item)
    {
        return [
            'id' => $item->book_id,
            'title' => $item->book_title,
            'summary' => $item->book_summary,
            'description' => $item->book_des,
            'picture' => $item->book_pic,
            'visitor_count' => $item->book_visitor,
            'is_new' => $item->book_is_new,
            'priority' => $item->book_priority,
            'date' => $item->book_date,
            'category' => $item->category,
            'type' => 'book',
            'type_label' => 'كتاب'
        ];
    }

    /**
     * Format video result
     */
    private function formatVideoResult($item)
    {
        return [
            'id' => $item->video_id,
            'title' => $item->video_title,
            'summary' => $item->video_summary,
            'description' => $item->video_des,
            'picture' => $item->video_pic,
            'visitor_count' => $item->video_visitor,
            'is_new' => $item->video_is_new,
            'priority' => $item->video_priority,
            'date' => $item->video_date,
            'youtube_id' => $item->video_youtube_id,
            'category' => $item->category,
            'type' => 'video',
            'type_label' => 'فيديو'
        ];
    }

    /**
     * Format sound result
     */
    private function formatSoundResult($item)
    {
        return [
            'id' => $item->sound_id,
            'title' => $item->sound_title,
            'summary' => $item->sound_summary,
            'description' => $item->sound_des,
            'picture' => $item->sound_pic,
            'visitor_count' => $item->sound_visitor,
            'is_new' => $item->sound_is_new,
            'priority' => $item->sound_priority,
            'date' => $item->sound_date,
            'category' => $item->category,
            'type' => 'sound',
            'type_label' => 'صوت'
        ];
    }

    /**
     * Format photo gallery result
     */
    private function formatPhotoGalleryResult($item)
    {
        return [
            'id' => $item->photo_gallery_id,
            'title' => $item->photo_gallery_title,
            'summary' => $item->photo_gallery_summary,
            'picture' => $item->photo_gallery_pic,
            'date' => $item->photo_gallery_date,
            'category' => $item->category,
            'type' => 'photo_gallery',
            'type_label' => 'معرض صور'
        ];
    }

    /**
     * Format page result
     */
    private function formatPageResult($item)
    {
        return [
            'id' => $item->pages_id,
            'title' => $item->pages_title,
            'content' => $item->pages_content,
            'date' => $item->pages_date,
            'type' => 'page',
            'type_label' => 'صفحة'
        ];
    }
}
