<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use App\Services\ResponseService;
use App\Services\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function filter(Request $request): JsonResponse
    {
        try {
            $news = $this->newsService->filter(
                $request->keyword,
                $request->date,
                $request->category,
                $request->source,
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

//    TODO: Create function to list news based on user preference

}
