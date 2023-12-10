<?php

namespace App\Table;

use App\Table\Core\Table;

class CompanyTable extends Table{
    protected function buildTable() : void
    {
        $this->setHeaders([
            [
                'title' => 'Name',
                'key' => 'name',
            ],
            [
                'title'=>"Siret",
                'key'=>'siret'
            ],
            [
                'title'=>"VAT Number",
                'key'=>'vatNumber'
            ]
        ]);

        $this->setItemsActions([
            [
                'content'=>'Show',
                'icon'=>'eye',
                "href"=> [
                    'path'=>'app_company_show',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                    ]
                ],
            ],
            [
                'content'=>'Edit',
                'icon'=>'create',
                "href"=> [
                    'path'=>'app_company_edit',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                    ]
                ],
            ],
            [
                'content'=>'Delete',
                'icon'=>'trash',
                "href"=> [
                    'csrf'=> fn($item)=> 'delete' . $item->getId(),
                    'method'=>'post',
                    'path'=>'app_company_delete',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content'=>"Create company",
                'icon'=>'add-circle-outline',
                'href'=>[
                    'path'=>'app_company_new',
                    'params'=>[]
                ],
            ]
        ]);
    }
}