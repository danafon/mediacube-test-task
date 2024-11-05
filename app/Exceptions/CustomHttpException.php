<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class CustomHttpException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json(
            [
                'errors' => [
                    [
                        'message' => $this->getMessage(),
                        'status' => $this->getCode(),
                    ],
                ],
            ],
            $this->getCode()
        );
    }
}
