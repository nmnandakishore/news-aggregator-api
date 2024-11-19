<?php

namespace App\Services;

use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class NewsService
{
    private News $news;
    private UserService $userService;

    public function __construct(News $news, UserService  $userService)
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
        } catch (Exception $exception){
            throw $exception;
        }
    }

    /**
     * Paginated list of all news
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function listAll(int $page = null, int $pageSize = null): array
    {
        try {
            return $this->news
                ->paginate($pageSize ?? 10, ['*'], 'page', $page ?? 1)
                ->appends(['pageSize' => $pageSize ?? 10])
                ->toArray();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Paginated list of news filtered with keyword, date, category or source
     * @param $keyword
     * @param $date
     * @param $category
     * @param $source
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function filter($keyword = null, $date = null, array $category = null, array $source = null, array $author = null, int $page = null, int $pageSize = null): array
    {
        try {
            dump($source);
            $news = $this->news;
            $searchKey = '%' . $keyword . '%';

            if ($keyword) {
                $news = $news
                    ->where('title', 'like', $searchKey)
                    ->orWhere('description', 'like', $searchKey)
                    ->orWhere('content', 'like', $searchKey)
                    ->orWhere('category', 'like', $searchKey);
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

        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * List news using users personalized filters
     * @param User $user
     * @param int|null $page
     * @param int|null $pageSize
     * @return array
     * @throws Exception
     */
    public function listPersonalized(User $user, int $page = null, int $pageSize = null): array
    {
        try {
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
        } catch (Exception $exception){
            throw $exception;
        }
    }

    public function getFilters(): array
    {
        try {
            $filters = $this->news
                ->select('category', 'source', 'author')
                ->distinct()
                ->get();

            return [
                'categories' => array_values($filters->pluck('category')->unique()->toArray()),
                'source' => array_values($filters->pluck('source')->unique()->toArray()),
                'author' => array_values($filters->pluck('author')->unique()->toArray()),
            ];
        } catch (Exception $exception){
            throw $exception;
        }
    }

}
