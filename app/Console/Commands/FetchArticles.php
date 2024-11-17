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
                        'author' => $article['author'] ?? null,
                        'published_at' => $article['publishedAt'],
                    ]
                );
            }
            $this->info('NewsAPI articles fetched successfully.');
        } else {
            $this->error('Failed to fetch articles from NewsAPI.');
        }
    }

    private function fetchFromGuardianApi()
    {
        $apikey = env('GUARDIAN_API_KEY');
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $apikey,
            'section' => 'technology',
            'show-fields' => 'headline,bodyText,webUrl',
            'page-size' => 10,
        ]);

        if ($response->ok()){
            $articles = $response->json()['response']['results'] ?? [];
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['url' => $article['webUrl']],
                    [
                        'title' => $article['fields']['headline'],
                        'description' => $article['fields']['bodyText'],
                        'url' => $article['webUrl'],
                        'source' => 'The Guardian',
                        'category' => $article['sectionName'] ?? 'General',
                        'author' => null,
                        'published_at' => $article['webPublicationDate'],
                    ]
                );
            }
            $this->info('The Guardian articles fetched successfully.');
        } else {
            $this->error('Failed to fetch articles from The Guardian.');
        }
    }

    private function fetchFromNewYorkTimesApi()
    {
        $apiKey = env('NYTIMES_API_KEY');
        $query = 'technology'; // Replace with the topic or keyword you want to search for
        $beginDate = now()->subWeek()->format('Ymd'); // Last week's articles
        $endDate = now()->format('Ymd'); // Today's date

        $response = Http::get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
            'api-key' => $apiKey,
            'q' => $query, // Keyword-based search
            'begin_date' => $beginDate, // Start date
            'end_date' => $endDate, // End date
            'fq' => 'section_name:("Technology")', // Filter by section (optional)
        ]);

        if ($response->ok()){
            $articles = $response->json()['response']['docs'] ?? [];
            foreach ($articles as $article) {
                Article::updateOrCreate(
                    ['url' => $article['web_url']],
                    [
                        'title' => $article['headline']['main'],
                        'description' => $article['snippet'] ?? null,
                        'url' => $article['web_url'],
                        'source' => 'New York Times',
                        'category' => $article['section_name'] ?? 'General',
                        'author' => $article['byline']['original'] ?? null,
                        'published_at' => $article['pub_date'],
                    ]
                );
            }
            $this->info('New York Times articles fetched successfully.');
        } else {
            $this->error('Failed to fetch articles from New York Times.');
        }
    }

}
