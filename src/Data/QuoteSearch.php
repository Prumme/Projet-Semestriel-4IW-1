<?php

namespace App\Data;

use App\Entity\Customer;

class QuoteSearch
{
    public ?Customer $customer;
    public ?string $status;

    public mixed $submit;
    public ?int $page;
    public function __construct()
    {
        $this->page = 1;
    }
}