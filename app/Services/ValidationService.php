<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    /**
     * @param $data
     * @param $rules
     * @param array $messages
     * @param array $customAttributes
     * @return void
     * @throws ValidationException
     */
    public function validate($data, $rules, $messages = [], $customAttributes = []): void
    {
        $validator = Validator::make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

}
