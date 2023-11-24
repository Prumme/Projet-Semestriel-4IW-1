<?php

namespace App\Security;

class AuthentificableRoles
{
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN'; //manage the whole app
    const ROLE_COMPANY_ADMIN = 'ROLE_COMPANY_ADMIN'; //manage a company
    const ROLE_USER = 'ROLE_USER'; //can see and edit his own data
}
