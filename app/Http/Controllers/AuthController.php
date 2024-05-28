<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\ErrorResponse;
use App\Helpers\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'loginID' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return new ErrorResponse($validator->errors(), 422);
            }

            $user = User::where('loginID', $request->loginID)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return new ErrorResponse('Invalid credentials', 401);
            }

            $token = $user->createToken($request->loginID)->plainTextToken;

            return new SuccessResponse([
                'role' => $user->role->name,
                'token' => $token
            ]);
        }
        catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        try {
            Auth::user()->currentAccessToken()->delete();
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function unauthorized(): JsonResponse
    {
        return new ErrorResponse('Unauthorized', 401);
    }
}
