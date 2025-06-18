<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'attachment_path' => $this->attachment_path,
            'executed_at' => $this->executed_at,
            'bank_account' => new BankAccountResource($this->whenLoaded('bankAccount')),
            'counterparty' => new CounterpartyResource($this->whenLoaded('counterparty')),
            'transaction_type' => new TransactionTypeResource($this->whenLoaded('transactionType')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
