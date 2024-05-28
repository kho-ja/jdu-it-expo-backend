<?php

namespace App\Http\Controllers;

use App\Models\BookCode;
use Illuminate\Http\Request;

class BookCodesController extends Controller
{
    public function lost($id) {
        try {
            $book = BookCode::find($id) ?? null;
            if ($book) {
                switch ($book->status) {
                    case "Mavjud":
                        $book->status = "Yo`qolgan";
                        $book->lost_date = date('d-m-Y');
                        $book->lost_by = auth()->user()->name;
                        $book->save();
                        break;
                    case "Ijarada":
                        return response()->json([
                            'status' => 'error',
                            'code' => 1,
                            'message' => "Ijarada bo'lgan kitobni Yo`qolgan qilish mumkin emas!"
                        ]);
                    case "Yo`qolgan":
                        return response()->json([
                            'status' => 'error',
                            'code' => 1,
                            'message' => "Kitob allaqachon yo'qolgan"
                        ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 1,
                    'message' => "Siz yuborgan Book ID noto'g'ri"
                ]);
            }
            return response()->json([
                'status' => 'success',
                'code' => 0,
                'message' => "Kitob holati Yo'qolgan ga o'zgartirildi",
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
    public function delete($id) {
        try {
            $book = BookCode::find($id) ?? null;
            if ($book) {
                switch ($book->status) {
                    case "Mavjud":
                        return response()->json([
                            'status' => 'success',
                            'code' => 0,
                            'message' => "Bu kitob to'liq o'chirildi",
                        ]);
                    case "Ijarada":
                        return response()->json([
                            'status' => 'error',
                            'code' => 1,
                            'message' => "Ijarada bo'lgan kitobni Yo'qolgan qilish mumkin emas!"
                        ]);
                    case "Yo`qolgan":
                        return response()->json([
                            'status' => 'error',
                            'code' => 1,
                            'message' => "Kitob allaqachon yo'qolgan"
                        ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 1,
                    'message' => "Siz yuborgan Book ID noto'g'ri"
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ]);
        }
    }
}
