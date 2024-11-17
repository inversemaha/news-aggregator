<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
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

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return response()->json($article);
    }
}
