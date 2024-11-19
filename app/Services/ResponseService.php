<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{

    /**
     * Construct JSON response
     * @param null $message
     * @param null $data
     * @param bool $flat
     * @return JsonResponse
     */
    public function sendJson($message = null, $data = null, $flat = false, $responseCode = 200): JsonResponse
    {
        if ($message) {
            $responseBody = [
                'message' => $message,
            ];
        }
        if ($data) {
            $responseBody['data'] = $data;
        }
        if ($flat) {
            $responseBody = $data;
        }
        return response()->json($responseBody)->setStatusCode($responseCode);
    }

}
