<?php

namespace App\Table;

use App\Table\Core\Table;

class InvoiceTable extends Table
{
    protected array $neededData = ['company'];

    protected function buildTable(): void
    {
        $company = $this->data['company'];

        $this->setHeaders([
            // [
            //     'title' => 'Invoice',
            //     'key' => 'name',
            // ],
            [
                'title' => 'Quote ID',
                'key' => 'id',
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
        ]);

        $this->setItemsActions([
            [
                'content' => 'See Invoice',
                'icon' => 'eye',
                "href" => [
                    'path' => 'app_invoice_show',
                    'params' => [
                        'company' => $company->getId(),
                        'id' => fn ($item) => $item->getId(),
                    ]
                ],
            ],
        ]);
    }
}
