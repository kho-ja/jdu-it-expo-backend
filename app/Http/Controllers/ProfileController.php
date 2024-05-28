<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileInfoResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        // Validate the incoming request
        $validatedData = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if the old password matches the current password
        if (Hash::check($validatedData['old_password'], $user->password)) {
            // Update the user's password
            $user->password = Hash::make($validatedData['new_password']);
            $user->save();

            return response()->json([
                'message' => 'Password updated successfully'
            ]);
        } else {
            return response()->json([
                'message' => 'Old password does not match'
            ], 422);
        }
    }

    public function getInfo() {
        try {
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Profile Info\'s retrieved successfully',
                'data' => ProfileInfoResource::make(\auth()->user())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 1,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updateImage(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/users');
                \auth()->user()->image = Storage::url($path);
                \auth()->user()->save();
                $msg = 'Profile image updated successfully';
            } else {
                \auth()->user()->image = '/storage/users/default.png';
                \auth()->user()->save();
                $msg = 'Profile image deleted successfully';

            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 1,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
