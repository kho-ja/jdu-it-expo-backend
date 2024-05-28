<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookCategoriesForStatResource;
use App\Http\Resources\DepartmentForStatResource;
use App\Http\Resources\GradeForStatResource;
use App\Models\BookCode;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\MainID;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function statsBook() {
        try {
            $books = BookCode::all();
            $categories = BookCategoriesForStatResource::collection(MainID::all());
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'All Library Stats retrieved successfully',
                'books' => [
                    'all' => $books->count(),
                    'in_library' => $books->where('status', 'Mavjud')->count(),
                    'in_rent' => $books->where('status', 'Ijarada')->count(),
                    'lost' => $books->where('status', "Yo`qolgan")->count()
                ],
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function stats() {
        try {
            $students = User::where('role_id', 1)->get();
            $professors = User::where('role_id', 2)->get();
            $employees = User::whereNotIn('role_id', [1, 2])->get();
            $departments = DepartmentForStatResource::collection(Department::all());

            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'All Stats retrieved successfully',
                'students' => [
                    'all' => $students->count(),
                    'active' => $students->where('status', 'active')->count(),
                    'tugallagan' => $students->where('status', 'tugallagan')->count(),
                ],
                'professors' => [
                    'all' => $professors->count(),
                    'active' => $professors->where('status', 'active')->count(),
                    'ketgan' => $professors->where('status', 'ketgan')->count(),
                ],
                'employees' => [
                    'all' => $employees->count(),
                    'active' => $employees->where('status', 'active')->count(),
                    'ketgan' => $employees->where('status', 'ketgan')->count(),
                ],
                'departments' => $departments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function statsLesson() {
        try {
            $professors = User::where('role_id', 2)->get();
            $departments = DepartmentForStatResource::collection(Department::all());
            $grades = GradeForStatResource::collection(Grade::whereNotIn('id', [5])->get());
            $lessons = Lesson::all();
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'All Lessons Stats retrieved successfully',
                'all' => count($lessons),
                'by_grade' => $grades
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
