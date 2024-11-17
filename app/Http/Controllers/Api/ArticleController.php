<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     summary="Get paginated articles with filtering options",
     *     tags={"Articles"},
     *     @OA\Parameter(name="keyword", in="query", description="Search keyword", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="date", in="query", description="Filter by publication date", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="category", in="query", description="Filter by category", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="source", in="query", description="Filter by source", required=false, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Successful response with paginated articles"),
     *     @OA\Response(response=401, description="Unauthorized access")
     * )
     */
    public function index(Request $request)
    {
        // Generate a unique cache key based on query parameters
        $cacheKey = 'articles_' . bcrypt(serialize($request->all()));

        // Fetch data from the cache if available, or execute the query and store the result in the cache
        $articles = cache()->remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Article::query();

            if ($request->filled('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('description', 'like', '%' . $request->keyword . '%');
            }

            if ($request->filled('date')) {
                $query->whereDate('published_at', $request->date);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('source')) {
                $query->where('source', $request->source);
            }

            return $query->paginate(10);
        });

        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     summary="Get a specific article by ID",
     *     tags={"Articles"},
     *     @OA\Parameter(name="id", in="path", description="Article ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful response with article details"),
     *     @OA\Response(response=404, description="Article not found"),
     *     @OA\Response(response=401, description="Unauthorized access")
     * )
     */

    public function show($id)
    {
        $article = Cache::remember("article_{$id}", now()->addMinutes(10), function () use ($id) {
            return Article::findOrFail($id);
        });

        return response()->json($article);
    }
}
