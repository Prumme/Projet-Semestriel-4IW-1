<?php

namespace App\Table;

use App\Table\Core\Table;

class CustomersTable extends Table{
    protected array $neededData = ['company'];
    protected function buildTable() : void
    {
        $company = $this->data['company'];

        $this->setHeaders([
            [
                'title' => 'Identity',
                'component' => 'customer/identity_cell.html.twig',
                'key' => 'lastname',
            ],
            [
                'title'=>"Company",
                'component'=>'customer/company_cell.html.twig',
                'key'=>'companyName',
            ],
            [
                'title'=>"Phone",
                'key'=>'tel'
            ]
        ]);

        $this->setItemsActions([
            [
                'content'=>'Show',
                'icon'=>'eye',
                "href"=> [
                    'path'=>'app_customer_show',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
            [
                'content'=>'Edit',
                'icon'=>'create',
                "href"=> [
                    'path'=>'app_customer_edit',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
            [
                'content'=>'Delete',
                'icon'=>'trash',
                "href"=> [
                    'csrf'=> fn($item)=> 'delete' . $item->getId(),
                    'method'=>'post',
                    'path'=>'app_customer_delete',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content'=>"Add customer",
                'icon'=>'add-circle-outline',
                'href'=>[
                    'path'=>'app_customer_new',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}