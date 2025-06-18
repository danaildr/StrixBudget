<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'amount_from' => $this->amount_from,
            'currency_from' => $this->currency_from,
            'amount_to' => $this->amount_to,
            'currency_to' => $this->currency_to,
            'exchange_rate' => $this->exchange_rate,
            'description' => $this->description,
            'executed_at' => $this->executed_at,
            'from_account' => new BankAccountResource($this->whenLoaded('fromAccount')),
            'to_account' => new BankAccountResource($this->whenLoaded('toAccount')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
