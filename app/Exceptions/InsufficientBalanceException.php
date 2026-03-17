<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Insufficient funds',
            'message' => $this->getMessage()
        ], 400); // 400 Bad Request
    }
}