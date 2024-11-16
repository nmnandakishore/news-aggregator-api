<?php

namespace App\Services\NewsProviders;

use App\Interfaces\NewsProviderInterface;
use App\Models\News;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;use function Pest\Laravel\get;
class NewsAPIProvider implements NewsProviderInterface
{

    private $sources;

    /**
     * Fetch news of last 1 day from News API
     * @throws ConnectionException
     */
    public function fetchNews(): array
    {

        $this->fetchSources();

         $newsResponse = Http::retry(3, 5000)
        ->withQueryParameters([
            "apiKey" => config('services.api_key.news_api'),
            'q' => "*",
            "from" => Carbon::now()->subtract('day', 1)->subtract('hour', 2)->toDateTimeLocalString(),
        ])
        ->get('https://newsapi.org/v2/everything')->object();

        $allNews = [];
        foreach ($newsResponse->articles as $article) {
            $source = $this->getSource($article->source->name);

            if($article->title == '[Removed]' || !$article->title || !$article->url || !$article->content || !$source){
                continue;
            }
            $allNews[] = $this->createNewsArray($article, $source);
        }
        return $allNews;
    }

    /**
     * Fetch sources from source API and assign to the class level variable
     * @return void
     * @throws ConnectionException
     */
    private function fetchSources(): void{
         $sourceResponse = Http::retry(3, 5000)
        ->withQueryParameters(["apiKey" => config('services.api_key.news_api'),])
        ->get("https://newsapi.org/v2/top-headlines/sources")->object();

         $this->sources = $sourceResponse->sources;
    }

    /**
     * Provide source object from the class level variable for the given source name
     * @param $sourceName
     * @return object|false
     */
    private function getSource($sourceName): object|false {
        $source = array_filter($this->sources, function ($source) use ($sourceName) {
            return $source->name == $sourceName;
        });
        return count($source) ? $source[key($source)] : false;
    }

    /**
     * Create news array according to the News model for given article and source
     * @param $article
     * @param $source
     * @return array
     */
    private function createNewsArray($article, $source): array{
        return [
            'title' => $article->title,
            'description' => $article->description ?? null,
            'url' => $article->url,
            'image' => $article->urlToImage ?? null,
            'content' => $article->content,
            'author' => $article->author ?? null,
            'category' => $source->category ?? 'uncategorized',
            'source' =>  $article->source->name ?? null,
            'language' => $source->language ?? null,
            'provider' => 'News API',
            'published_at' => $article->publishedAt ? Carbon::createFromTimeString($article->publishedAt) : null,
            'country' => $source->country ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
//            'tags' =>'string',
        ];
    }
}

