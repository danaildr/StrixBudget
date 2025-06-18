<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
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
            'name' => $this->name,
            'currency' => $this->currency,
            'balance' => $this->balance,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'transactions_count' => $this->when(isset($this->transactions_count), $this->transactions_count),
            'total_income' => $this->when(isset($this->total_income), $this->total_income),
            'total_expenses' => $this->when(isset($this->total_expenses), $this->total_expenses),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
