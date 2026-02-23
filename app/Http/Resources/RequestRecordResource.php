<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestRecordResource extends JsonResource
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
            'client_name' => $this->client_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'problem_text' => $this->problem_text,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'assigned_to_user' => $this->assignedTo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
