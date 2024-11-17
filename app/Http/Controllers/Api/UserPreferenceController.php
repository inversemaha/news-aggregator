<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/preferences",
     *     summary="Store user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Preferences updated successfully"),
     *     @OA\Response(response=401, description="Unauthorized access")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/personalized-feed",
     *     summary="Get personalized feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful response with personalized feed"),
     *     @OA\Response(response=401, description="Unauthorized access")
     * )
     */

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
