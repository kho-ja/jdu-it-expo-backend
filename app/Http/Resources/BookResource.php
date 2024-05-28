<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category->code ?? null,
            'language' => $this->language ?? null,
            'quantity' => count($this->codes),
            'name' => $this->name,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'publish_year' => $this->publish_year,
        ];
    }
}
