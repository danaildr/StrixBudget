<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\TransactionType;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\Counterparty;
use App\Models\BankAccount;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the bank accounts for the user.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get the default bank account for the user.
     */
    public function defaultBankAccount()
    {
        return $this->bankAccounts()->where('is_default', true)->first();
    }

    /**
     * Get the counterparties for the user.
     */
    public function counterparties(): HasMany
    {
        return $this->hasMany(Counterparty::class);
    }

    /**
     * Get the transfers for the user.
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the transaction types for the user.
     */
    public function transactionTypes(): HasMany
    {
        return $this->hasMany(TransactionType::class);
    }

    public function scheduledPayments()
    {
        return $this->hasMany(\App\Models\ScheduledPayment::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is power user
     */
    public function isPowerUser(): bool
    {
        return $this->role === 'power_user';
    }

    /**
     * Check if user can manage registration keys
     */
    public function canManageRegistrationKeys(): bool
    {
        return $this->isAdmin() || $this->isPowerUser();
    }

    /**
     * Check if user can manage user roles
     */
    public function canManageUserRoles(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the registration keys created by this user
     */
    public function createdRegistrationKeys(): HasMany
    {
        return $this->hasMany(RegistrationKey::class, 'created_by');
    }
}
