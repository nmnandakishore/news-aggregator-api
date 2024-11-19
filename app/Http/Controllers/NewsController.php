<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use App\Services\ResponseService;
use App\Services\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isNull;

class NewsController extends Controller
{

    private ValidationService $validationService;
    private ResponseService $responseService;
    private NewsService $newsService;

    public function __construct(
        ValidationService $validationService,
        ResponseService   $responseService,
        NewsService       $newsService
    )
    {
        $this->validationService = $validationService;
        $this->responseService = $responseService;
        $this->newsService = $newsService;
    }

    /**
     * Paginated list of all news
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function listAll(Request $request): JsonResponse
    {
        try {
            $news = $this->newsService->listAll($request->integer('page'), $request->integer('pageSize'));
            return $this->responseService->sendJson(null, $news, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Paginated list of news filtered with keyword, date, category or source
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function listFiltered(Request $request): JsonResponse
    {
        try {
            $news = $this->newsService->filter(
                $request->keyword,
                $request->date,
                $request->category ? [$request->category] : null,
                $request->source ? [$request->source] : null,
                null,
                $request->integer('page'),
                $request->integer('pageSize')
            );
            return $this->responseService->sendJson(null, $news, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get single news by id
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function getById(Request $request): JsonResponse
    {
        try {
            $news = $this->newsService->getById($request->id);
            return $this->responseService->sendJson(null, $news, true);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * List preferred news
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function listPersonalized(Request $request): JsonResponse
    {
        try {
            $preferredNews = $this->newsService->listPersonalized($request->user(),$request->integer('page'),$request->integer('pageSize'));
            return $this->responseService->sendJson(null, $preferredNews, true);
        } catch (Exception $exception){
            throw $exception;
        }
    }

    public function getFilters(Request $request): JsonResponse
    {
        try {
            $filters = $this->newsService->getFilters();
            return $this->responseService->sendJson(null, $filters);
        } catch (Exception $exception){
            throw $exception;
        }

    }
}
