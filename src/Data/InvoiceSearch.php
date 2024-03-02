<?php

namespace App\Data;

use App\Entity\Customer;

class InvoiceSearch
{
    public ?Customer $customer;
    public ?string $status;
    public $quote;
    public mixed $submit;
    public ?int $page;
    public function __construct()
    {
        $this->page = 1;
    }
}