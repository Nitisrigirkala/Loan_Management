<?php

// App/Services/ResponseService.php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    public function sendError($error, $errorMessages = [], $code = 400): JsonResponse
    {
        // Default to a generic error message if no specific error message is provided
        $response = [
            'success' => false,
            'message' => $error,
        ];

        // If there are validation errors, extract and use the first one
        if (!empty($errorMessages)) {
            // Convert the MessageBag to an array and get the first error message
            $firstError = collect($errorMessages)->flatten()->first();
            $response['message'] = $firstError ? $firstError : $error;
        }

        return response()->json($response, $code);
    }

    public function sendResponse($result, $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
}
