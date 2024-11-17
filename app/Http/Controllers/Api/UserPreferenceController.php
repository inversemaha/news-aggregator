<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'array',
            'sources' => 'array',
            'author' => 'array',
        ]);

        $preference = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only('categories', 'sources', 'author')
         );

        return response()->json(['message' => 'User preference has been updated successfully.']);
    }

    public function personalizedFeed(Request $request)
    {
        $preferences = $request->user()->preference;

        $articles = Article::query()
            ->whereIn('category', $preferences->categories ?? [])
            ->orWhereIn('source', $preferences->sources ?? [])
            ->paginate(10);

        return response()->json($articles);
    }
}
