<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RegistrationKey extends Model
{
    protected $fillable = [
        'key',
        'description',
        'role',
        'is_used',
        'used_at',
        'used_by',
        'created_by',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    /**
     * Generate a unique registration key
     */
    public static function generateKey(): string
    {
        do {
            $key = Str::random(32);
        } while (self::where('key', $key)->exists());

        return $key;
    }

    /**
     * Mark the key as used
     */
    public function markAsUsed(User $user): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by' => $user->id,
        ]);
    }

    /**
     * Check if the key is valid for use
     */
    public function isValid(): bool
    {
        return !$this->is_used;
    }

    /**
     * Get the user who created this key
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who used this key
     */
    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
