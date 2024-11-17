<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from NewsAPI, The Guardian, and New York Times';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching articles...');
        // Fetch articles from NewsAPI
        $this->fetchFromNewsAPI();

        // Fetch articles from The Guardian
        $this->fetchFromGuardianApi();

        // Fetch articles from New York Times
        $this->fetchFromNewYorkTimesApi();

        $this->info('Articles have been fetched successfully.');
    }

    private function fetchFromNewsAPI()
    {
        $apiKey = env('NEWS_API_KEY');
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => $apiKey,
            'country' => 'us',
            'pageSize' => 10,
        ]);

        if ($response->ok()){
            $articles = $response->json()['articles'] ?? [];
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['url' => $article['url']],
                    [
                        'title' => $article['title'],
                        'description' => $article['description'],
                        'url' => $article['url'],
                        'source' => 'NewsAPI',
                        'category' => $article['source']['name'] ?? 'General',
                        'published_at' => $article['publishedAt'],
                    ]
                );
            }
            $this->info('NewsAPI articles fetched successfully.');
        } else {
            $this->error('Failed to fetch articles from NewsAPI.');
        }
    }


}
