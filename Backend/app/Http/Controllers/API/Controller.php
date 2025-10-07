<?php

namespace App\Http\Controllers\API;

use App\Http\Middleware\ValidateApiKey;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        $this->middleware(ValidateApiKey::class);
    }

    /**
     * Send a JSON response.
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($status = true, $data = [],$message,$statusCode)
    {
        $response = [
            'status' => $status,
            'responseData' => $data,
            'message' => $message,
        ];

        return response()->json($response,$statusCode);
    }

}
