<?php

namespace App\Services\NewsProviders;

use App\Interfaces\NewsProviderInterface;
use App\Models\News;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;use function Pest\Laravel\get;
class GuardianAPIProvider implements NewsProviderInterface
{

//    private $sections;

    /**
     * Fetch news of last 1 day from News API
     * @throws ConnectionException
     */
    public function fetchNews(): array
    {

         $newsResponse = Http::retry(3, 5000)
        ->withQueryParameters([
            "api-key" => config('services.api_key.guardian_api'),
            "page-size" => 50,
            "show-tags" => true
        ])
        ->get('https://content.guardianapis.com/search')->object();

        $allNews = [];

        foreach ($newsResponse->response->results as $article) {
            $allNews[] = $this->createNewsArray($article);
        }
        return $allNews;
    }


    /**
     * Create news array according to the News model for given article and source
     * @param $article
     * @param $source
     * @return array
     */
    private function createNewsArray($article): array{
        return [
            'title' => $article->webTitle,
            'description' => null,
            'url' => $article->webUrl,
            'image' =>  null,
            'content' => $article->webTitle,
            'author' => null,
            'category' => $source->sectionName ?? 'uncategorized',
            'source' =>  'The Guardian' ?? null,
            'language' => null,
            'provider' => 'Guardian API',
            'published_at' => $article->webPublicationDate ? Carbon::createFromTimeString($article->webPublicationDate) : null,
            'country' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'tags' => ($article->tags && is_array($article->tags)) ? implode(',', $article->tags) : null,
        ];
    }
}

