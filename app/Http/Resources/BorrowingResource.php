<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowingResource extends JsonResource
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
            'user_id' => $this->user_id,
            'book' => collect($this->book)->except(['created_at','updated_at', 'stock']),
            'loan_duration' => $this->loan_duration,
            'borrowed_at' => $this->borrowed_at,
            'return_due_at' => $this->return_due_at,
            'status' => $this->status,
        ];
    }
}
