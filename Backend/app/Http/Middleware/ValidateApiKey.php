<?php
// Define custom middleware to validate the API key
namespace App\Http\Middleware;

use Closure;

class ValidateApiKey
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-Api-Key');

        // Check if the API key is valid
        if ($apiKey !== ENV('API_KEY')) {
            $response = [
                'status' => false,
                'responseData' => [],
                'message' => 'Unauthorized Invalid X-Api-Key',
            ];
            return response()->json($response,401);
        }
    return $next($request);
    }
}
