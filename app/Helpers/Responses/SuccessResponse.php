<?php

namespace App\Helpers\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct($data=[], $message="", $status=200)
    {
        $data = [
            "success" => true,
            'code' => $status,
            "message" => $message,
            "data" => $data
        ];

        parent::__construct($data, $status);
    }
}


