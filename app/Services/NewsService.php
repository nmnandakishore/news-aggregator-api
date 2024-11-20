<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NewsService
{
    private News $news;
    private UserService $userService;

    public function __construct(News $news, UserService $userService)
    {
        $this->news = $news;
        $this->userService = $userService;
    }

    /**
     * Paginated list of news filtered with keyword, date, category or source
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function getById(int $id): array
    {
        try {
            return $this->news->where('id', $id)->first()->toArray();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Cached and paginated list of all news
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function listAll(int $page = null, int $pageSize = null): array
    {
        try {
            return Cache::remember('listAll' . $page . $pageSize, 300, function () use ($page, $pageSize) {
                return $this->news
                    ->paginate($pageSize ?? 10, ['*'], 'page', $page ?? 1)
                    ->appends(['pageSize' => $pageSize ?? 10])
                    ->toArray();
            });
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Cached and paginated list of news filtered with keyword, date, category or source
     * @param null $keyword
     * @param null $date
     * @param array|null $category
     * @param array|null $source
     * @param array|null $author
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function filter($keyword = null, $date = null, array $category = null, array $source = null, array $author = null, int $page = null, int $pageSize = null): array
    {
        try {
            $categoryForCacheName = ($category && sizeof($category)) ? implode(',', $category) : "";
            $sourceForCacheName = ($source && sizeof($source)) ? implode(',', $source) : "";
            $authorForCacheName = ($author && sizeof($author)) ? implode(',', $author) : "";
            $cacheName = 'filter' . $keyword . $date . $categoryForCacheName . $sourceForCacheName . $authorForCacheName . $page . $pageSize;

            return Cache::remember($cacheName, 300, function () use ($keyword, $date, $category, $source, $author, $page, $pageSize) {
                $news = $this->news;
                $searchKey = '%' . $keyword . '%';

                if ($keyword) {
                    $news = $news
                        ->where('title', 'ilike', $searchKey)
                        ->orWhere('description', 'ilike', $searchKey)
                        ->orWhere('content', 'ilike', $searchKey)
                        ->orWhere('category', 'ilike', $searchKey);
                }
                if ($date) {
                    $news = $news->whereDate('published_at', Carbon::createFromFormat('d-m-Y', $date));
                }
                if ($category) {
                    $news = $news->whereIn('category', $category);
                }
                if ($source) {
                    $news = $news->whereIn('source', $source);
                }
                if ($author) {
                    $news = $news->whereIn('$author', $source);
                }

                return $news
                    ->paginate($pageSize ?? 10, ['*'], 'page', $page ?? 1)
                    ->appends(['pageSize' => $pageSize ?? 10])
                    ->toArray();
            });

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Cached list of news using users personalized filters
     * @param User $user
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function listPersonalized(User $user, int $page = null, int $pageSize = null): array
    {
        try {
            $cacheName = 'filter' . $user->id . $page . $pageSize;
            var_dump($cacheName);

            return Cache::remember($cacheName, 300, function () use ($user, $page, $pageSize) {

                $preferences = collect($this->userService->listPreferences($user));
                $preferredCategories = $preferences->where('preference_name', 'categories')->first();
                $preferredAuthors = $preferences->where('preference_name', 'authors')->first();
                $preferredSources = $preferences->where('preference_name', 'sources')->first();

                $news = $this->news;
                if ($preferredCategories) {
                    $news = $news->OrWhereIn('category', json_decode($preferredCategories['value']));
                }
                if ($preferredSources) {
                    $news = $news->OrWhereIn('source', json_decode($preferredSources['value']));
                }
                if ($preferredAuthors) {
                    $news = $news->OrWhereIn('author', (json_decode($preferredAuthors['value'])));
                }

                $result = $news
                    ->paginate($pageSize ?? 10, ['*'], 'page', $page ?? 1)
                    ->appends(['pageSize' => $pageSize ?? 10])
                    ->toArray();

                return $result;
            });
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Cached list of all available filters
     * @return array
     * @throws Exception
     */
    public function getFilters(): array
    {
        try {
            return Cache::remember("filters", 300, function () {
                $filters = $this->news
                    ->select('category', 'source', 'author')
                    ->distinct()
                    ->get();

                return [
                    'categories' => array_values($filters->pluck('category')->unique()->toArray()),
                    'source' => array_values($filters->pluck('source')->unique()->toArray()),
                    'author' => array_values($filters->pluck('author')->unique()->toArray()),
                ];
            });
        } catch (Exception $exception) {
            throw $exception;
        }
    }

}
