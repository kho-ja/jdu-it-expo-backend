<?php

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category' => $this->category->code ?? null,
            'language' => $this->language,
            'name' => $this->name,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'publish_year' => $this->publish_year,
            'codes' => BookCodesResource::collection($this->codes)
        ];
    }
}
