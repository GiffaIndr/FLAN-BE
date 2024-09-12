<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'family_name' => $this->family_name,
            'description' => $this->description,
            'address' => $this->address,
            'created_at' => $this->created_at->format('d/m/Y'),
            'update_at' => $this->update_at->format('d/m/Y'),
        ];
    }
}
