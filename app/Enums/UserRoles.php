<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'Admin';
    case CUSTOMER = 'Customer';

    public function label(): string
    {
        return match($this){
            static::ADMIN => 'Admin',
            static::CUSTOMER => 'Customer',
        };
    }
}
