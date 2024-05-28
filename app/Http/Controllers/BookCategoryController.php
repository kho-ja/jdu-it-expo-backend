<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\ErrorResponse;
use App\Helpers\Responses\SuccessResponse;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookCategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = BookCategory::all();
            return new SuccessResponse($categories);
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $category = new BookCategory();
            $category->create($request->all());

            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Category created successfully.',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = BookCategory::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $category = BookCategory::findOrFail($id);
            $category->update($request->all());
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $category = BookCategory::findOrFail($id);
            $category->delete();
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }
}
