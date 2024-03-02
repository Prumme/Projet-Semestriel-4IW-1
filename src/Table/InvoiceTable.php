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
             [
                 'title' => 'Invoice',
                 'key' => 'invoiceNumber',
             ],
            [
                'title' => 'Quote',
                'key' => 'quoteNumber',
            ],
            [
                'title' => 'Customer',
                'component' => 'quote/customer_identity_cell.html.twig',
                'key' => 'customer',
            ],
            [
                'title' => 'Emited Date',
                'component' => 'components/tables/table_date_cell.html.twig',
                'key' => 'emittedAt',
            ],
            [
                'title' => 'Expired At',
                'component' => 'components/tables/table_date_cell.html.twig',
                'key' => 'expiredAt',
            ],
            [
                'title' => 'Payment Status',
                'component' => 'invoice/payment_status_cell.html.twig',
                'key' => 'status',
            ]
        ]);

        $this->setItemsActions([
            [
                'content' => 'See Invoice',
                'icon' => 'eye',
                "href" => [
                    'path' => 'app_invoice_edit',
                    'params' => [
                        'company' => $company->getId(),
                        'invoice' => fn ($item) => $item->getId(),
                    ]
                ],
            ],
        ]);
    }
}
