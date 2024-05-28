<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MainController extends Controller
{
    public function test(): string
    {
        return 'Connection successfully!';
    }

    public function homepage(Request $request): SuccessResponse
    {
        $user = $request->user();

        if ($user->role->name == 'librarian') {
            return new SuccessResponse([
               'title' => 'Hello Librarian',
               'description' => 'Banner Description for Librarian',
               'image' => '/storage/banner/librarian-homepage.png',
               'video_link' => 'https://youtube.com'
            ]);
        } else {
            return new SuccessResponse([
                'title' => 'Hello Student',
                'description' => 'Banner Description for Student',
                'image' => '/storage/banner/student-homepage.png',
                'video_link' => 'https://youtube.com'
            ]);
        }
    }
}
