<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\ErrorResponse;
use App\Helpers\Responses\SuccessResponse;
use App\Http\Resources\RentResource;
use App\Models\Book;
use App\Models\BookCode;
use App\Models\RentBook;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentBookController extends Controller
{
    public function index() {
        try {
            $rents = RentBook::where('status', 'Ijarada')
                ->orderBy('created_at', 'desc')
                ->get();

            return new SuccessResponse(RentResource::collection($rents));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

//    public function rentInfo(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'user_id' => 'required|exists:users,loginID',
//            'book_id' => 'required|exists:book_codes,code',
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'status' => 'error',
//                'code' => 1,
//                'message' => 'Validation error',
//                'errors' => $validator->errors()
//            ]);
//        }
//        $user = User::where('loginID', $request->user_id)->first();
//        $book = BookCode::where('code', $request->book_id)->first();
//        $readyForRent = $this->checkReadyForRent($user, $book);
//        if ($readyForRent) {
//            return $readyForRent; // Return the error response from the helper function
//        }
//        $today = new DateTime();
//        $futureDate =(clone $today)->modify('+7 days');
//        return response()->json([
//            'status' => 'success',
//            'code' => 0,
//            'message' => 'User and Book Information\'s retrieved successfully',
//            'given_date' => $today->format('d-m-Y'),
//            'return_date' => $futureDate->format('d-m-Y'),
//            'user' => UserForRentResource::make($user),
//            'book' => BookForRentResource::make($book)
//        ]);
//    }

    public function rentBook(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,loginID',
            'book_id' => 'required|exists:book_codes,code',
        ]);

        if ($validator->fails()) {
            return new ErrorResponse($validator->errors()->first());
        }
        try {
            $user = User::where('loginID', $request->user_id)->first();
            $book = BookCode::where('code', $request->book_id)->first();

            $readyForRent = $this->checkReadyForRent($book);
            if ($readyForRent) {
                return $readyForRent; // Return the error response from the helper function
            }

            $today = date('d-m-Y');
            $futureDate = date('d-m-Y', strtotime('+7 days'));

            $rent = new RentBook();
            $rent->book_id = $book->book_id;
            $rent->book_code = $book->code;
            $rent->user_id = $user->id;
            $rent->give_date = $today;
            $rent->return_date = $futureDate;
            $rent->given_by = $request->user()->name;
            $rent->save();
            $book->given_date = $today;
            $this->changeStatus('Ijarada', $book);
            return new SuccessResponse(RentResource::make($rent));
        } catch (Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function returnBook(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'book_id' => 'required|exists:book_codes,code',
                'code' => 'required'
            ]);

            if ($validator->fails()) {
                return new ErrorResponse($validator->errors()->first());
            }
            $book = BookCode::where('code', $request->book_id)->first();
            $rent = RentBook::where('book_code', $request->book_id)->first();
            $readyForReturn = $this->checkReadyForReturn($rent, $book, $request->code);
            if ($readyForReturn) {
                return $readyForReturn; // Return the error response from the helper function
            }
            $rent->taken_by = $request->user()->name;
            $rent->returned_date = date('d-m-Y');
            $this->changeStatus("Qabul qilindi", $rent);
            $this->changeStatus("Mavjud", $book);
            return new SuccessResponse([], 'Rent Closed Successfully');
        } catch (ModelNotFoundException $e) {
            return new ErrorResponse('Problem! The submitted book or this book rent was not found in the database', 404);
        } catch (Exception $e) {
            return new ErrorResponse( $e->getMessage());
        }
    }

    public function expireRents(): SuccessResponse
    {
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();

        $rentBooks = RentBook::where('status', 'Ijarada')
            ->whereDate('give_date', '<', $sevenDaysAgo)
            ->orderBy('created_at', 'desc')
            ->get();
        return new SuccessResponse(RentResource::collection($rentBooks));
    }

//    public function lostBook($id, Request $request) {
//        try {
//            $rent = RentBook::findOrFail($id);
//            $book = BookCode::where('code', $rent->book_code)->first();
//            $readyForLost = $this->checkReadyForLost($rent, $book);
//            if ($readyForLost) {
//                return $readyForLost; // Return the error response from the helper function
//            }
//            $rent->lost_comment = $request->comment ?? null;
//            $user = auth()->user()->name;
//            $date = date('d-m-Y');
//            $rent->taken_by = $user;
//            $book->lost_by = $user;
//            $rent->returned_date = $date;
//            $book->lost_date = $date;
//            $book->save();
//            $this->changeStatus("Yo`qolgan", $rent);
//            $this->changeStatus("Yo`qolgan", $book);
//            return response()->json([
//                'status' => 'success',
//                'code' => 0,
//                'message' => 'With the loss of the book, the book rental was closed',
//                'data' => ArchiveResource::make($rent)
//            ]);
//
//        } catch (Exception $e) {
//            return response()->json([
//                'status' => 'error',
//                'code' => $e->getCode(),
//                'message' => $e->getMessage(),
//                'data' => []
//            ], 500);
//        }
//    }
//    public function archive() {
//        try {
//            $archive = RentBook::whereIn('status', ['Qabul qilindi', "Yo`qolgan"])
//                ->orderBy('returned_date')
//                ->get();
//            return response()->json([
//                'status' => 'success',
//                'code' => 0,
//                'message' => 'Archive retrieved successfully',
//                'data' => ArchiveResource::collection($archive)
//            ]);
//
//        } catch (Exception $e) {
//            return response()->json([
//                'status' => 'error',
//                'code' => $e->getCode(),
//                'message' => $e->getMessage(),
//                'data' => []
//            ], 500);
//        }
//    }

//    public function lostBooks() {
//        try {
//            $lost_books = BookCode::where('status', "Yo`qolgan")->get();
//
//            return response()->json([
//                'status' => 'success',
//                'code' => 0,
//                'message' => 'Lost Books retrieved successfully',
//                'data' => LostBookResource::collection($lost_books)
//            ]);
//        } catch (Exception $e) {
//            return response()->json([
//                'status' => 'error',
//                'code' => $e->getCode(),
//                'message' => $e->getMessage(),
//                'data' => []
//            ], 500);
//        }
//    }
    public function checkReadyForRent($book): bool|ErrorResponse
    {
        if ($book->status == "Ijarada") {
            return new ErrorResponse('This book status is already rented. Please check again');
        } elseif ($book->status == "Yo`qolgan") {
            return new ErrorResponse('This book is from the list of lost books. In this case, it is not possible to rent');
        }
        return false;
    }

    public function checkReadyForReturn($rent, $book, $code): bool|ErrorResponse
    {
        if (!$book) {
            return new ErrorResponse('This book code is invalid');
        } elseif ($book->status == "Mavjud") {
            return new ErrorResponse('The status of this book is active and it has been returned to the library.');
        } elseif ($book->status == "Yo`qolgan") {
            return new ErrorResponse('This book is on the lost books list. Please check again');
        }
        if (!$rent) {
            return new ErrorResponse('There was no rental for this book');
        } elseif ($rent->status == "Qabul qilindi") {
            return new ErrorResponse('This book rental has already closed');
        } elseif ($rent->status == "Yo`qolgan") {
            return new ErrorResponse('This rent is closed with the loss of the book');
        } elseif (!$rent->return_code) {
            return new ErrorResponse('The student did not generate code to close Rent');
        } elseif ($rent->return_code != $code) {
            return new ErrorResponse('Invalid Code');
        }
        return false;
    }
    public function checkReadyForLost($rent, $book): bool|ErrorResponse
    {
        if ($rent->status != "Ijarada" && $book->status != "Ijarada") {
            return new ErrorResponse('This book cannot be lost. This book is not in rental status');
        }
        return false;
    }

    public function changeStatus($status, $client) {
        $client->status = $status;
        $client->save();
    }
}
