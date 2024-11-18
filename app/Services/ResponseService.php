<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{

    /**
     * Construct JSON response
     * @param $message
     * @param $data
     * @return JsonResponse
     */
    public function sendJson($message, $data=null){
       $responseBody = [
           'message' => $message,
       ];
       if ($data) {
           $responseBody['data'] = $data;
       }
       return response()->json($responseBody);
   }

}
