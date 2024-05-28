<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role' => $this->role->name ?? null,
            'is_dekan' => $this->is_dekan ?? 0,
            'fio' => $this->name,
            'loginID' => $this->loginID,
            'passport' => $this->passport ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'image' => $this->image ?? '/storage/users/default.png',
        ];
    }
}
