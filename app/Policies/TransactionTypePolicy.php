<?php

namespace App\Policies;

use App\Models\TransactionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TransactionType $transactionType): bool
    {
        return $user->id === $transactionType->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TransactionType $transactionType): bool
    {
        return $user->id === $transactionType->user_id;
    }

    public function delete(User $user, TransactionType $transactionType): bool
    {
        return $user->id === $transactionType->user_id;
    }
} 