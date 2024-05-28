<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'loginID' => $this->loginID,
            'name' => $this->name,
            'japan_group_id' => $this->japanGroup->name ?? null,
            'it_group_id' => $this->itGroup->name ?? null,
            'info' => $this->info,
            'image' => $this->image,
        ];
    }
}
