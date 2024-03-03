<?php

namespace App\Security;

class AuthentificableRoles
{
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN'; //manage the whole app
    const ROLE_COMPANY_ADMIN = 'ROLE_COMPANY_ADMIN'; //manage a company
    const ROLE_USER = 'ROLE_USER'; //can see and edit his own data

    public static function hierarchy() : array
    {
        return [
            1 => self::ROLE_SUPER_ADMIN,
            2 => self::ROLE_COMPANY_ADMIN,
            3 => self::ROLE_USER,
        ];
    }
}
