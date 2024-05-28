<?php

namespace App\Http\Controllers;

use App\Helpers\Responses\ErrorResponse;
use App\Helpers\Responses\SuccessResponse;
use App\Http\Resources\BookDetailResource;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\BookCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(): ErrorResponse|SuccessResponse
    {
        try {
            $books = Book::all();
            return new SuccessResponse(BookResource::collection($books));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = $this->validator($request);
            if ($validator->fails()) {
                return new ErrorResponse($validator->errors(), 422);
            }

            $create = $request->all();
            unset($create['quantity']);

            $book = Book::create($create);

            Book::generateBookCodes($request->quantity, $book);

            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Book created successfully.',
                'book' => BookResource::make($book)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): ErrorResponse|SuccessResponse
    {
        try {
            $book = Book::findOrFail($id);
            return new SuccessResponse(BookDetailResource::make($book));
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $validator = $this->validator($request);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }
            $book = Book::findOrFail($id);
            if ($book->quantity < $request->quantity) {
                Book::generateBookCodes($request, $book, $book->quantity + 1);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 0,
                    'message' => 'Kitob miqdorini kamaytirish mumkin emas! Buni Book Detailda Lost orqali qilishingiz mumkin',
                ]);
            }
            $book->name = $request->input('name');
            $book->author = $request->input('author');
            $book->publisher = $request->input('publisher');
            $book->volume = $request->input('volume');
            $book->main_id = $request->input('main_id');
            $book->mid_id = $request->input('mid_id');
            $book->language_id = $request->input('language_id');
            $book->category_id = $request->input('category_id');

            $book->save();
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Book updated successfully',
                'data' => $book
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
            $book_codes = BookCode::where('book_id', $id)->delete();
            $book = Book::findOrFail($id)->delete();
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Book deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }

    public function validator($request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required',
            'author' => 'required',
            'publisher' => 'required',
            'publish_year' => 'required',
            'category_id' => 'required',
            'quantity' => 'required',
        ]);
    }

    public function addBook(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'book_id' => 'required',
                'add_count' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 400,
                    'message' => $validator->errors()->first(),
                ], 400);
            }

            $book = Book::findOrFail($request->book_id);
            $latest = BookCode::all()->count();

            for ($i = (count($book->codes)+1); $i <= count($book->codes)+$request->add_count; $i++) {
                $number_of_library = sprintf('%04d', $latest+1);
                $latest++;
                $number_of_book = sprintf('%02d', $i);
                $code = $book->category->category_short . $book->language->language_short . $book->mid_id . $number_of_library . 'c.' . $book->volume . 'v.' . $number_of_book;
                BookCode::create([
                    'book_id' => $book->id,
                    'number_of_book' => $number_of_book,
                    'number_of_library' => $number_of_library,
                    'code' => $code,
                ]);
            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => 'Book count increment successfully',
                'data' => BookDetailResource::make($book),
//                'codes' => BookCodesResource::collection($book->codes)
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
