<?php

namespace App\Services\NewsProviders;

use App\Interfaces\NewsProviderInterface;
use App\Models\News;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use function Pest\Laravel\get;

class NYTimesAPIProvider implements NewsProviderInterface
{

    /**
     * Fetch last 10 news from NY Times API
     * @throws ConnectionException
     */
    public function fetchNews(): array
    {

        $newsResponse = Http::retry(3, 5000)
            ->withQueryParameters([
                "api-key" => config('services.api_key.ny_times_api'),
            ])
            ->get('https://api.nytimes.com/svc/search/v2/articlesearch.json')->object();

        $allNews = [];

        foreach ($newsResponse->response->docs as $article) {
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
    private function createNewsArray($article): array
    {
        $imageUrl = $this->getImage($article);
        return [
            'title' => $article->snippet,
            'description' => $article->snippet ?? null,
            'url' => $article->web_url,
            'image' => $imageUrl,
            'content' => $article->lead_paragraph,
            'author' => null,
            'category' => $article->section_name ?? 'uncategorized',
            'source' => $article->source ?? null,
            'language' => null,
            'provider' => 'TNY Times API',
            'published_at' => $article->pub_date ? Carbon::createFromTimeString($article->pub_date) : null,
            'country' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Retrive image URL from article
     * @param $article
     * @return mixed
     */
    private function getImage($article): mixed
    {
        $image = null;
        if ($article->multimedia) {
            $image = collect($article->multimedia)->where('type', 'image')->first();
        }

        return (null !== $image) ? 'https://www.nytimes.com/' . $image->url : null;
    }
}

