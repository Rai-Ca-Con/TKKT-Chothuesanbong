<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'distance'  => $this->distance,
            'duration'  => $this->duration,
            'address'   => $this->address,
            'price'     => $this->price,
            'description' => $this->description,
            'category'  => new CategoryResource($this->whenLoaded('category')),
            'state'     => new StateResource($this->whenLoaded('state')),
            'images'    => ImageResource::collection($this->whenLoaded('images')),
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];

    }
}
