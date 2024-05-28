<?php

namespace App\Helpers\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

class ErrorResponse extends JsonResponse
{
    public function __construct($message, $status = 500)
    {
        $data = [
            "success" => false,
            'code' => $status,
            "message" => $message,
            "data" => null
        ];

        parent::__construct($data,$status);
    }

}
