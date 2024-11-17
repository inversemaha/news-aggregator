<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        // Invalidate cache for the updated article
        Cache::forget("article_{$article->id}");

        // Optionally clear article list cache if necessary
        Cache::tags('articles')->flush();
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        // Invalidate cache for the deleted article
        Cache::forget("article_{$article->id}");

        // Optionally clear article list cache if necessary
        Cache::tags('articles')->flush();
    }

    /**
     * Handle the Article "restored" event.
     */
    public function restored(Article $article): void
    {
        //
    }

    /**
     * Handle the Article "force deleted" event.
     */
    public function forceDeleted(Article $article): void
    {
        //
    }
}
