<?php

namespace App\Services;

use App\Models\Category;use App\Models\News;
use App\Services\NewsProviders\GuardianAPIProvider;
use App\Services\NewsProviders\NewsAPIProvider;
use Carbon\Carbon;use Illuminate\Support\Collection;
use function Illuminate\Log\log;use function Laravel\Prompts\info;

class NewsFetcher
{

    private const NEWS_PROVIDERS = [
        NewsAPIProvider::class,
        GuardianAPIProvider::class,
    ];

    /**
     * @var Collection
     */
    private Collection $providerObjects;

    /**
     * Invoke news fetch process when called by the scheduler
     * @return void
     */
    public function __invoke(): void
    {
        $this->providerObjects = collect([]);
        $this->registerNewsProviders();
        $this->fetchNews();
    }

    /**
     * Prepare provider objects
     * @return void
     */
    private function registerNewsProviders(): void
    {
        foreach (self::NEWS_PROVIDERS as $provider){
            $this->providerObjects->push(new $provider());
        }
    }

    /**
     * Fetch news from each news provider API and save into the database.
     * @return void
     */
    public function fetchNews(): void
    {
        foreach ($this->providerObjects as $newsProviderObj){
            $news = $newsProviderObj->fetchNews();
            info(((string)(sizeof($news)) ?? '0') . " news articles were fetched through     " . get_class($newsProviderObj) . " at " . Carbon::now()->toIso8601String());
            $this->saveNews($news);
        }
    }

    /**
     * Save given news into the database
     * This function is kept separately for any operation to be made before inserting into the database
     * @param $news
     * @return void
     */
    private function saveNews($news): void
    {
        News::insert($news);
    }
}
