<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class LoginController extends Controller
{
    protected $responseService;

    // Inject ResponseService via the constructor
    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Return validation errors if present
        if ($validator->fails()) {
            return $this->responseService->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user with the provided email exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email.',
            ], 404);
        }

        // Check login credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Generate token and retrieve authenticated user details
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        // Send success response
        return $this->responseService->sendResponse($success, 'User logged in successfully.');
    }
}
