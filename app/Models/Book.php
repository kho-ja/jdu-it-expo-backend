<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BookCategory::class);
    }
    public function codes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookCode::class);
    }


    public static function generateBookCodes($quantity, Book $book) {

        for ($i = 1; $i <= $quantity; $i++) {
            $code = $book->category->code . "-" . $i;
            BookCode::create([
                'book_id' => $book->id,
                'code' => $code,
            ]);
        }
    }

}
