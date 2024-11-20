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
     * @return array
     */
    public function buildResponse($message = null, $data = null, bool $flat = false): array
    {
        $responseBody = [];
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

        return $responseBody;
    }

}
