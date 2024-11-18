<?php

namespace App\Services;

use App\Models\News;
use Carbon\Carbon;
use Exception;

class NewsService
{
    private News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
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
    public function filter($keyword = null, $date = null, $category = null, $source = null, int $page = null, int $pageSize = null): array
    {
        try {
            $news = $this->news;
            $searchKey = '%' . $keyword . '%';

            if ($keyword) {
                $news = $news
                    ->where('title', 'like', $searchKey)
                    ->orWhere('description', 'like', $searchKey)
                    ->orWhere('content', 'like', $searchKey)
                    ->orWhere('category', 'like', $searchKey);
            }
            if ($category) {
                $news = $news->where('category', $category);
            }
            if ($date) {
                $news = $news->whereDate('published_at', Carbon::createFromFormat('d-m-Y', $date));
            }
            if ($source) {
                $news = $news->where('source', $source);
            }

            return $news
                ->paginate($pageSize ?? 10, ['*'], 'page', $page ?? 1)
                ->appends(['pageSize' => $pageSize ?? 10])
                ->toArray();

        } catch (Exception $exception) {
            throw $exception;
        }
    }

//    TODO: Create function to list news based on user preference

}
