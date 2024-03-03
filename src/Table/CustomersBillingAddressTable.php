<?php

namespace App\Table;

use App\Table\Core\Table;

class CustomersBillingAddressTable extends Table
{
    protected array $neededData = ['customer','company'];
    protected function buildTable(): void
    {
        $company = $this->data['company'];
        $customer = $this->data['customer'];

        $this->setHeaders([
            [
                'title' => 'Address lines',
                'component' => 'billingAddress/address_line_cell.html.twig',
                'key' => 'addressLine1',
            ],
            [
                'title' => "City & Zip",
                'component' => 'billingAddress/city_zip_cell.html.twig',
                'key' => 'city',
            ],
            [
                'title' => "Country",
                'key' => 'countryCode',
            ]
        ]);

        $this->setItemsActions([
            [
                'content' => 'Edit',
                'icon' => 'create',
                "href" => [
                    'path' => 'app_customer_address_edit',
                    'params' => [
                        'id'=> fn ($item) => $item->getId(),
                        'customer' => $customer->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ],
            [
                'content' => 'Delete',
                'icon' => 'trash',
                "href" => [
                    'csrf' => fn ($item) => 'delete' . $item->getId(),
                    'method' => 'post',
                    'path' => 'app_customer_address_delete',
                    'params' => [
                        'id'=> fn ($item) => $item->getId(),
                        'customer' => $customer->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content' => "Add billing address",
                'icon' => 'add-circle-outline',
                'href' => [
                    'path' => 'app_customer_address_new',
                    'params' => [
                        'customer' => $customer->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}
