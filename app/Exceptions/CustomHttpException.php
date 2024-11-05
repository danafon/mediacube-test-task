<?php

namespace App\Exceptions;

use Exception;

abstract class CustomHttpException extends Exception
{
    public function render()
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
