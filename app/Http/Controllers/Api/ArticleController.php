<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

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
        $query = Article::query();

        if ($request->filled('keyword')){
            $query -> where('title', 'like', '%'.$request->keyword.'%')
                    ->orWhere('description', 'like', '%'.$request->keyword.'%');
        }

        if ($request->filled('date')){
            $query -> whereDate('published_at', $request->date);
        }

        if ($request->filled('category')){
            $query -> where('category', $request->category);
        }

        if ($request->filled('source')){
            $query -> where('source', $request->source);
        }

        return response()->json($query->paginate(10));
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
        $article = Article::findOrFail($id);
        return response()->json($article);
    }
}
