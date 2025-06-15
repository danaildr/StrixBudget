<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case POWER_USER = 'power_user';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::USER => 'User',
            self::POWER_USER => 'Power User',
            self::ADMIN => 'Administrator',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canManageRegistrationKeys(): bool
    {
        return in_array($this, [self::ADMIN, self::POWER_USER]);
    }

    public function canManageUserRoles(): bool
    {
        return $this === self::ADMIN;
    }
}
