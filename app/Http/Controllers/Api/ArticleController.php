<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::query()
            ->where($request->category, fn($query) => $query->where('category', $request->category))
            ->where($request->source, fn($query) => $query->where('source', $request->source))
            ->where($request->keyword, fn($query) => $query->where('title', 'like', "%{$request->keyword}%"))
            ->paginate(10);

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article);
    }
}
