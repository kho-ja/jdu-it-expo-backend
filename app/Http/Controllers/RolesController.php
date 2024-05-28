<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\UsersPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::all();
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Roles retrieved successfully',
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 2,
                'message' => 'Validation error',
                'data' => $validator->errors()
            ]);
        }

        try {
            $role = Role::create($request->all());
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Role created successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 2,
                'message' => 'Validation error',
                'data' => $validator->errors()
            ]);
        }

        try {
            $role = Role::findOrFail($id);
            $role->update($request->all());
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Role deleted successfully'
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
