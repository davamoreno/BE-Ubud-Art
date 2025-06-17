<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'Admin';
    case COSTUMER = 'Costumer';

    public function label(): string
    {
        return match($this){
            static::ADMIN => 'Admin',
            static::COSTUMER => 'Costumer',
        };
    }
}
