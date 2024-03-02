<?php

namespace App\Table;

use App\Table\Core\Table;

class QuoteTable extends Table
{
    protected array $neededData = ['company'];

    protected function buildTable(): void
    {
        $company = $this->data['company'];

        $this->setHeaders([
            [
                'title' => 'Quote',
                'key' => 'formattedNumber',
            ],
            [
                'title' => 'Customer',
                'component' => 'quote/customer_identity_cell.html.twig',
                'key' => 'customer',
            ],
            [
                'title' => 'Emited Date',
                'component' => 'components/tables/table_date_cell.html.twig',
                'key' => 'emitedAt',
            ],
            [
                'title' => 'Expired At',
                'component' => 'components/tables/table_date_cell.html.twig',
                'key' => 'expiredAt',
            ],
            [
                'title' => 'Status',
                'component' => 'quote/has_been_signed.html.twig',
                'key' => 'status',
            ]
        ]);

        $this->setItemsActions([
            [
                'content' => 'Edit',
                'icon' => 'eye',
                "href" => [
                    'path' => 'app_quote_edit',
                    'params' => [
                        'id' => fn ($item) => $item->getId(),
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
                    'path' => 'app_quote_delete',
                    'params' => [
                        'id' => fn ($item) => $item->getId(),
                        'company' => $company->getId(),
                    ]
                ],
            ]
        ]);

        $this->setGroupedActions([
            [
                'content'=>"delete",
                'icon'=>'trash',
                'href'=>[
                    'path'=>'app_quote_mass_delete',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);

        $this->setActions([
            [
                'content' => 'New Quote',
                'icon' => 'add',
                "href" => [
                    'path' => 'app_quote_new',
                    'params' => [
                        'company' => $company->getId(),
                    ]
                ],
            ],
        ]);
    }
}
