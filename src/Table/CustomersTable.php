<?php

namespace App\Table;

use App\Security\Voter\Attributes\CustomerVoterAttributes;
use App\Table\Core\Table;

class CustomersTable extends Table
{
    protected array $neededData = ['company','security'];
    protected function buildTable(): void
    {
        $company = $this->data['company'];
        $security = $this->data['security'];

        $this->setHeaders([
            [
                'title' => 'Identity',
                'component' => 'customer/identity_cell.html.twig',
                'key' => 'lastname',
            ],
            [
                'title' => "Company",
                'component' => 'customer/company_cell.html.twig',
                'key' => 'companyName',
            ],
            [
                'title' => "Phone",
                'key' => 'tel'
            ]
        ]);

        $this->setItemsActions([
            [
                'content' => 'Edit',
                'icon' => 'create',
                "href" => [
                    'path' => 'app_customer_edit',
                    'params' => [
                        'id' => fn ($item) => $item->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ],
            [
                'content' => 'Delete',
                'icon' => 'trash',
                'visible'=> fn($item) => $security->isGranted(CustomerVoterAttributes::CAN_DELETE_CUSTOMER,$item),
                "href" => [
                    'csrf' => fn ($item) => 'delete' . $item->getId(),
                    'method' => 'post',
                    'path' => 'app_customer_delete',
                    'params' => [
                        'id' => fn ($item) => $item->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content' => "Add customer",
                'icon' => 'add-circle-outline',
                'href' => [
                    'path' => 'app_customer_new',
                    'params' => [
                        'company' => $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}
