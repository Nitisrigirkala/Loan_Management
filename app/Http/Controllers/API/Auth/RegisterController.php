<?php

// App/Http/Controllers/API/Auth/RegisterController.php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Services\ResponseService;

class RegisterController extends Controller
{
    protected $responseService;

    // Inject ResponseService via the constructor
    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function register(Request $request): JsonResponse
    {
        // Adding unique validation for email
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email', // Check if email is unique
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->responseService->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->responseService->sendResponse($success, 'User registered successfully.');
    }
}
