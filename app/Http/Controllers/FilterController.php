<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminLessonResource;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\BookCategoriesForStatResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\EmployeesResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Models\AcademicYear;
use App\Models\Attachment;
use App\Models\Book;
use App\Models\BookCode;
use App\Models\Lesson;
use App\Models\MainID;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function filter($client, Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            switch ($client) {
                case 'professors':
                    $result = User::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = ProfessorResource::collection($result->where('role_id', 2)->get());
                    break;
                case 'students':
                    $result = User::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            if ($column == "group_id") {
                                $result->whereHas('groups', function ($query) use ($value) {
                                    $query->where('groups.id', $value);
                                });
                            } else {
                                $result->where($column, '=', $value);
                            }
                        }
                    }
                    $response = StudentResource::collection($result->where('role_id', 1)->get());
                    break;
                case 'employees':
                    $result = User::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = EmployeesResource::collection($result->whereNotIn('role_id', [1, 2])->get());
                    break;
                case 'lessons':
                    $result = Lesson::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = AdminLessonResource::collection($result->get());
                    break;
                case 'books':
                    $result = Book::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = BookResource::collection($result->get());
                    break;
                case 'plans':
                    $result = Plan::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = PlanResource::collection($result->where('academic_year_id', AcademicYear::currentYearID())->get());
                    break;
                case 'attachments':
                    $result = Attachment::query();
                    foreach ($request->query() as $column => $value) {
                        if (!empty($value)) {
                            $result->where($column, '=', $value);
                        }
                    }
                    $response = AttachmentResource::collection($result->where('academic_year_id', AcademicYear::currentYearID())->get());
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'code' => 1,
                        'message' => "Noto'g'ri client yuborilmoqda!"
                    ]);
            }
            if (count($response) == 0) {
                return response()->json([
                    'status' => 'error',
                    'code' => 1,
                    'message' => "Hech qanday malumot topilmadi!"
                ]);
            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Filtered '. ucfirst($client) . ' retrieved successfully',
                'page_size' => 1,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function filterItems($client): \Illuminate\Http\JsonResponse
    {
        try {
            switch ($client) {
                case 'professors':
                    $distinct = User::where('role_id', 2)->select('status')->distinct()->get();
                    break;
                case 'students':
                    $distinct = User::where('role_id', 1)->select('status')->distinct()->get();
                    break;
                case 'employees':
                    $distinct = User::whereNotIn('role_id', [1, 2])->select('status')->distinct()->get();
                    break;
                case 'plans':
                    $distinct = Plan::all()->select('status')->distinct()->get();
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'code' => 1,
                        'message' => "Noto'g'ri client!"
                    ]);
            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'data' => $distinct
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
