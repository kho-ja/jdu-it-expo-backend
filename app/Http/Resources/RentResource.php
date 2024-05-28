<?php

namespace App\Http\Resources;

use App\Models\Book;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class RentResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $givenDate = DateTime::createFromFormat('d-m-Y', $this->give_date);
        $today = new DateTime();
        $interval = $today->diff($givenDate);
        $days = $interval->days;
        $book = Book::find($this->book_id) ?? null;
        return [
            'id' => $this->id,
            'user_name' => $this->user->name ?? null,
            'user_id' => $this->user->loginID,
            'book_name' => $book->name ?? null,
            'book_author' => $book->author ?? null,
            'book_code' => $this->book_code,
            'rent_day' => $days,
            'give_date' => $this->give_date,
            'return_date' => $this->return_date,
            'librarian' => $this->given_by,
        ];
    }
}
