<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\ErrorResponse;
use App\Helpers\Responses\SuccessResponse;
use App\Http\Resources\AttachmentForStudentResource;
use App\Http\Resources\AttachmentsForProfessorResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\RentResource;
use App\Http\Resources\ScienceResource;
use App\Http\Resources\StudentResource;
use App\Models\AcademicYear;
use App\Models\Attachment;
use App\Models\BookCode;
use App\Models\Grade;
use App\Models\Group;
use App\Models\RentBook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Testing\Fluent\Concerns\Has;

class StudentController extends Controller
{
    public function index(): ErrorResponse|SuccessResponse
    {
        try {
            $users = User::where('role_id', 1)->where('status', 'active')->get();
            return new SuccessResponse(StudentResource::collection($users));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function store(Request $request): ErrorResponse|SuccessResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'loginID' => 'unique:users',
                'name' => 'required',
                'japan_group_id' => 'required',
                'it_group_id' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return new ErrorResponse($validator->errors()->first());
            }
            $create = $request->all();
            $create['password'] = Hash::make($create['password']);

            $user = User::create($create);
            return new SuccessResponse(StudentResource::make($user));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function show($id): ErrorResponse|SuccessResponse
    {
        try {
            $user = User::where('role_id', 1)->findOrFail($id);;
            return new SuccessResponse(StudentResource::make($user));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function update(Request $request, $id): ErrorResponse|SuccessResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'loginID' => 'unique:users',
                'name' => 'required',
                'japan_group_id' => 'required',
                'it_group_id' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return new ErrorResponse($validator->errors()->first());
            }

            $user = User::where('role_id', 1)->findOrFail($id);
            $user->update($request->all());
            return new SuccessResponse(StudentResource::make($user));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function destroy($id): ErrorResponse|SuccessResponse
    {
        try {
            $user = User::where('role_id', 1)->findOrFail($id);
            $user->delete();

            return new SuccessResponse();
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function getRents(Request $request): SuccessResponse
    {
        $user = $request->user();
        $rents =$user->rents->where('status', 'Ijarada');

        return new SuccessResponse(RentResource::collection($rents));
    }

    public function return(Request $request): ErrorResponse|SuccessResponse
    {
        $validator = Validator::make($request->all(), [
            'book_code' => 'required',
            'summary' => 'required',
        ]);
        if ($validator->fails()) {
            return new ErrorResponse($validator->errors()->first());
        }

        $book = BookCode::where('code', $request->book_code)->first();
        if (!$book) {
            return new ErrorResponse('This BookID not found', 404);
        }

        if ($book->status == "Ijarada"){
            $rent = RentBook::where('book_code', $book->code)->first();
            if (!$rent && $rent->status == 'Ijarada') {
                return new ErrorResponse('Rent problem. Please report this to the Administrators');
            }
            $code = substr(str_shuffle('123456789123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            $rent->return_code = $code;
            $rent->save();
            return new SuccessResponse(['code' => $code]);
        } else {
            return new ErrorResponse('This book is not ready to return. Book Status is not Rent');
        }
    }
}

