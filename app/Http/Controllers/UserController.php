<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use App\Services\ResponseService;
use App\Services\UserService;
use App\Services\ValidationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use function Pest\Laravel\json;

class UserController extends Controller
{

    private UserService $userService;
    private ResponseService $responseService;
    private ValidationService $validationService;

    public function __construct(UserService $userService, ResponseService $responseService, ValidationService $validationService)
    {
        $this->userService = $userService;
        $this->responseService = $responseService;
        $this->validationService = $validationService;
    }

    /**
     * List User Preferences
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function listPreferences(Request $request): JsonResponse
    {
        try {
            $preferences = $this->userService->listPreferences($request->user());
            return response()->json($this->responseService->buildResponse('user preferences', $preferences));
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Update user preferences
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        try {
            $this->validationService->validate($request->all(), UserPreference::$createRules);

            $preferences = [];
            foreach ($request->all() as $preference) {
                if (is_array($preference)) {
                    $preferenceObj = [
                        'preference_name' => $preference['name'],
                        'value' =>  json_encode($preference['value'])
                    ];
                    array_push($preferences, $preferenceObj);
                }
            }

            if($this->userService->updatePreferences($preferences, $request->user())){
                return response()->json($this->responseService->buildResponse('Preferences Updated', null, false), HttpResponse::HTTP_CREATED);
            }

            return response()->json($this->responseService->buildResponse('user preferences not updated'), HttpResponse::HTTP_INTERNAL_SERVER_ERROR);

        } catch (Exception $exception) {
            throw $exception;
        }
    }

}
