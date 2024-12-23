<?php

namespace App\Console\Commands;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            $this->fetchFromNewsAPI();
        } catch (\Exception $e) {
            $this->error('Error fetching articles from NewsAPI: ' . $e->getMessage());
        }

        // Fetch articles from The Guardian
        try {
            $this->fetchFromGuardianApi();
        } catch (\Exception $e) {
            $this->error('Error fetching articles from The Guardian: ' . $e->getMessage());
        }

        // Fetch articles from New York Times
        try {
            $this->fetchFromNewYorkTimesApi();
        } catch (\Exception $e) {
            $this->error('Error fetching articles from New York Times: ' . $e->getMessage());
        }

        $this->info('Articles have been fetched successfully.');
    }

    private function fetchFromNewsAPI()
    {
        try {
            $apiKey = config('api_keys.news_api');
            $response = Http::retry('3','100')->timeout(10)->get('https://newsapi.org/v2/top-headlines', [
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
                            'author' => $article['author'] ?? 'Unknown',
                            'published_at' => isset($article['publishedAt'])
                                ? Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s')
                                : now(),
                        ]
                    );
                }
                $this->info('NewsAPI articles fetched successfully.');
            } else {
                $this->warn('NewsAPI responded with status: ' . $response->status());
                Log::warning('NewsAPI response issue', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error('NewsAPI request failed: ' . $e->getMessage());
        }
    }

    private function fetchFromGuardianApi()
    {
        try {
            $apikey = config('api_keys.guardian_api');
            $response = Http::retry('3','100')->timeout(10)->get('https://content.guardianapis.com/search', [
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
                            'published_at' => isset($article['webPublicationDate'])
                                ? Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s')
                                : now(),
                        ]
                    );
                }
                $this->info('The Guardian articles fetched successfully.');
            } else {
                $this->warn('The Guardian responded with status: ' . $response->status());
                Log::warning('The Guardian response issue', ['response' => $response->body()]);
            }

        } catch (\Exception $e) {
            $this->error('The Guardian request failed: ' . $e->getMessage());
        }
    }

    private function fetchFromNewYorkTimesApi()
    {
        try {
            $apiKey = config('api_keys.nytimes_api');
            $query = 'technology'; // Replace with the topic or keyword you want to search for
            $beginDate = now()->subWeek()->format('Ymd'); // Last week's articles
            $endDate = now()->format('Ymd'); // Today's date

            $response = Http::retry('3','100')->timeout(10)->get('https://api.nytimes.com/svc/search/v2/articlesearch.json', [
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
                            'published_at' => isset($article['pub_date'])
                                ? Carbon::parse($article['pub_date'])->format('Y-m-d H:i:s')
                                : now(),
                        ]
                    );
                }
                $this->info('New York Times articles fetched successfully.');
            } else {
                $this->warn('New York Times responded with status: ' . $response->status());
                Log::warning('New York Times response issue', ['response' => $response->body()]);
            }

        } catch (\Exception $e) {
            $this->error('New York Times request failed: ' . $e->getMessage());
        }
    }

}
