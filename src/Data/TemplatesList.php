<?php

namespace App\Data;

use Doctrine\ORM\Query\Expr;

class TemplatesList {
    const WELCOME_EMAIL = 1;
    const FORGET_PASSWORD = 3;
    const WELCOME_EMAIL_OWNER = 4;
    const CONTACT_US = 5;
    const NEW_QUOTE_OPENED = 6;
    const SIGNED_URL_EXPIRED = 7;
    const NEW_INVOICE = 8;
    const PAYMENT_REMINDER = 10;
    const EXPIRED_INVOICE_ANNOUNCEMENT = 11;
}