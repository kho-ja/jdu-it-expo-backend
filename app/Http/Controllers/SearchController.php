<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminLessonResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\EmployeesResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Models\Book;
use App\Models\Lesson;
use App\Models\Plan;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($client, Request $request) {
        try {
            switch ($client) {
                case 'professors':
                    $results = ProfessorResource::collection(\App\Models\User::where('role_id', 2)
                        ->where(function ($query) use ($request) {
                            $query->where('loginID', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('name', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('phone', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('passport', 'LIKE', '%'.$request->key.'%');
                        })->get());
                    break;
                case 'students':
                    $results = StudentResource::collection(\App\Models\User::where('role_id', 1)
                        ->where(function ($query) use ($request) {
                            $query->where('loginID', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('name', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('phone', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('passport', 'LIKE', '%'.$request->key.'%');
                        })->get());
                    break;
                case 'employees':
                    $results = EmployeesResource::collection(\App\Models\User::whereNotIn('role_id', [1, 2])
                        ->where(function ($query) use ($request) {
                            $query->where('loginID', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('name', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('phone', 'LIKE', '%'.$request->key.'%')
                                ->orWhere('passport', 'LIKE', '%'.$request->key.'%');
                        })->get());
                    break;
                case 'books':
                    $results = BookResource::collection(Book::where('name', 'LIKE', '%'.$request->key.'%')
                        ->orWhere('author', 'LIKE', '%'.$request->key.'%')
                        ->orWhere('publisher', 'LIKE', '%'.$request->key.'%')
                        ->get());
                    break;
                case 'lessons':
                    $results = AdminLessonResource::collection(Lesson::where(function ($query) use ($request) {
                        $query->whereHas('user', function ($subQuery) use ($request) {
                            $subQuery->where('users.name', 'LIKE', '%' . $request->key . '%');
                        })->orWhere('lessons.title', 'LIKE', '%' . $request->key . '%');
                    })->get());
                    break;
                case 'plans':
                    $results = PlanResource::collection(Plan::where('name', 'LIKE', '%'.$request->key.'%')
                        ->get());
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'code' => 1,
                        'message' => "Bu yerda search ishlamaydi!"
                    ]);
            }

            $msg = "Searched $client retrieved successfully";
            if (count($results) == 0) {
                $msg = "Qidirilgan '$request->key' Malumotlar bazasidan topilmadi";
            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => $msg,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
