<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\VideoCategoryController;
use App\Http\Controllers\Api\SoundController;
use App\Http\Controllers\Api\SoundCategoryController;
use App\Http\Controllers\Api\PhotoGalleryController;
use App\Http\Controllers\Api\PhotoGalleryCategoryController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\MainSectionsController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SimpleSectionsController;
use App\Http\Controllers\Api\HierarchyController;
use App\Http\Controllers\Api\CategoryHierarchyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()
    ]);
});

// Test endpoint
Route::get('/test', [TestController::class, 'test']);

// Hierarchy endpoints
Route::prefix('hierarchy')->controller(HierarchyController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/simple', 'simple');
});

// Category Hierarchy endpoints
Route::prefix('categories')->controller(CategoryHierarchyController::class)->group(function () {
    Route::get('/menu/{menuId}', 'getByMenu');
    Route::get('/{type}/main', 'getMainCategories');
    Route::get('/{type}/{categoryId}/subcategories', 'getSubcategories');
    Route::get('/{type}/{categoryId}/content', 'getContentByCategory');
});

/*
|--------------------------------------------------------------------------
| Menu Routes
|--------------------------------------------------------------------------
*/
Route::prefix('menus')->group(function () {
    Route::get('/', [MenuController::class, 'index']);
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::get('/position/{position}', [MenuController::class, 'byPosition']);
    Route::get('/header/active', [MenuController::class, 'header']);
});

/*
|--------------------------------------------------------------------------
| Link Routes
|--------------------------------------------------------------------------
*/
Route::prefix('links')->group(function () {
    Route::get('/', [LinkController::class, 'index']);
    Route::get('/{id}', [LinkController::class, 'show']);
    Route::get('/menu/{menuId}', [LinkController::class, 'byMenu']);
});

/*
|--------------------------------------------------------------------------
| Article Routes
|--------------------------------------------------------------------------
*/
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{id}', [ArticleController::class, 'show']);
    Route::get('/new/latest', [ArticleController::class, 'new']);
    Route::get('/priority/featured', [ArticleController::class, 'priority']);
    Route::get('/category/{categoryId}', [ArticleController::class, 'byCategory']);
});

/*
|--------------------------------------------------------------------------
| Article Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('article-categories')->group(function () {
    Route::get('/', [ArticleCategoryController::class, 'index']);
    Route::get('/{id}', [ArticleCategoryController::class, 'show']);
    Route::get('/main/parents', [ArticleCategoryController::class, 'main']);
    Route::get('/subcategories/{parentId}', [ArticleCategoryController::class, 'subcategories']);
    Route::get('/hierarchy/tree', [ArticleCategoryController::class, 'hierarchy']);
});

/*
|--------------------------------------------------------------------------
| Book Routes
|--------------------------------------------------------------------------
*/
Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::get('/new/latest', [BookController::class, 'new']);
    Route::get('/priority/featured', [BookController::class, 'priority']);
    Route::get('/category/{categoryId}', [BookController::class, 'byCategory']);
    Route::get('/format/{format}', [BookController::class, 'byFormat']);
});

/*
|--------------------------------------------------------------------------
| Book Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('book-categories')->group(function () {
    Route::get('/', [BookCategoryController::class, 'index']);
    Route::get('/{id}', [BookCategoryController::class, 'show']);
    Route::get('/main/parents', [BookCategoryController::class, 'main']);
    Route::get('/subcategories/{parentId}', [BookCategoryController::class, 'subcategories']);
    Route::get('/hierarchy/tree', [BookCategoryController::class, 'hierarchy']);
});

/*
|--------------------------------------------------------------------------
| Video Routes
|--------------------------------------------------------------------------
*/
Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/{id}', [VideoController::class, 'show']);
    Route::get('/new/latest', [VideoController::class, 'new']);
    Route::get('/priority/featured', [VideoController::class, 'priority']);
    Route::get('/category/{categoryId}', [VideoController::class, 'byCategory']);
    Route::get('/youtube/all', [VideoController::class, 'youtube']);
});

/*
|--------------------------------------------------------------------------
| Video Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('video-categories')->group(function () {
    Route::get('/', [VideoCategoryController::class, 'index']);
    Route::get('/{id}', [VideoCategoryController::class, 'show']);
    Route::get('/main/parents', [VideoCategoryController::class, 'main']);
    Route::get('/subcategories/{parentId}', [VideoCategoryController::class, 'subcategories']);
    Route::get('/hierarchy/tree', [VideoCategoryController::class, 'hierarchy']);
});

/*
|--------------------------------------------------------------------------
| Sound Routes
|--------------------------------------------------------------------------
*/
Route::prefix('sounds')->group(function () {
    Route::get('/', [SoundController::class, 'index']);
    Route::get('/{id}', [SoundController::class, 'show']);
    Route::get('/new/latest', [SoundController::class, 'new']);
    Route::get('/priority/featured', [SoundController::class, 'priority']);
    Route::get('/category/{categoryId}', [SoundController::class, 'byCategory']);
});

/*
|--------------------------------------------------------------------------
| Sound Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('sound-categories')->group(function () {
    Route::get('/', [SoundCategoryController::class, 'index']);
    Route::get('/{id}', [SoundCategoryController::class, 'show']);
    Route::get('/main/parents', [SoundCategoryController::class, 'main']);
    Route::get('/subcategories/{parentId}', [SoundCategoryController::class, 'subcategories']);
    Route::get('/hierarchy/tree', [SoundCategoryController::class, 'hierarchy']);
});

/*
|--------------------------------------------------------------------------
| Photo Gallery Routes
|--------------------------------------------------------------------------
*/
Route::prefix('photo-galleries')->group(function () {
    Route::get('/', [PhotoGalleryController::class, 'index']);
    Route::get('/{id}', [PhotoGalleryController::class, 'show']);
    Route::get('/new/latest', [PhotoGalleryController::class, 'new']);
    Route::get('/category/{categoryId}', [PhotoGalleryController::class, 'byCategory']);
});

/*
|--------------------------------------------------------------------------
| Photo Gallery Category Routes
|--------------------------------------------------------------------------
*/
Route::prefix('photo-gallery-categories')->group(function () {
    Route::get('/', [PhotoGalleryCategoryController::class, 'index']);
    Route::get('/{id}', [PhotoGalleryCategoryController::class, 'show']);
    Route::get('/main/parents', [PhotoGalleryCategoryController::class, 'main']);
    Route::get('/subcategories/{parentId}', [PhotoGalleryCategoryController::class, 'subcategories']);
    Route::get('/hierarchy/tree', [PhotoGalleryCategoryController::class, 'hierarchy']);
});

/*
|--------------------------------------------------------------------------
| Page Routes
|--------------------------------------------------------------------------
*/
Route::prefix('pages')->group(function () {
    Route::get('/', [PageController::class, 'index']);
    Route::get('/{id}', [PageController::class, 'show']);
    Route::get('/new/latest', [PageController::class, 'new']);
    Route::get('/priority/featured', [PageController::class, 'priority']);
});

/*
|--------------------------------------------------------------------------
| Block Routes
|--------------------------------------------------------------------------
*/
Route::prefix('blocks')->group(function () {
    Route::get('/', [BlockController::class, 'index']);
    Route::get('/{id}', [BlockController::class, 'show']);
    Route::get('/new/latest', [BlockController::class, 'new']);
    Route::get('/priority/featured', [BlockController::class, 'priority']);
});

/*
|--------------------------------------------------------------------------
| Main Sections Routes (Menu-Page Hierarchy)
|--------------------------------------------------------------------------
*/
Route::prefix('main-sections')->group(function () {
    Route::get('/', [MainSectionsController::class, 'index']);
    Route::get('/{id}', [MainSectionsController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Simple Sections Routes (Simplified Menu-Page Hierarchy)
|--------------------------------------------------------------------------
*/
Route::prefix('sections')->group(function () {
    Route::get('/', [SimpleSectionsController::class, 'index']);
    Route::get('/{id}', [SimpleSectionsController::class, 'show']);
});

