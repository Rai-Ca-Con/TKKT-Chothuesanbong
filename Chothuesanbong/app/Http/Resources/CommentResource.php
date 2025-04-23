<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fieldId' => $this->field_id,
            'content' => $this->content,
            'user' => new UserResource($this->whenLoaded('user')),
            'child' => CommentResource::collection($this->whenLoaded('children')),
            'image_url' => $this->image_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
