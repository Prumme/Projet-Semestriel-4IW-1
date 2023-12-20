<?php

namespace App\Table;

use App\Table\Core\Table;

class ProductsTable extends Table{
    protected array $neededData = ['company'];
    protected function buildTable() : void
    {
        $company = $this->data['company'];

        $this->setHeaders([
            [
                'title' => 'Name',
                'key' => 'name',
            ],
            [
                'title'=>"Description",
                'key'=>'description',
            ],
            [
                'title'=>"Price",
                //'component'=>'product/price_cell.html.twig',
                'key'=>'price'
            ],
            [
                'title'=>"Categories",
                'component'=>'product/categories_cell.html.twig',
                'key'=>'categories',
            ]
        ]);

        $this->setItemsActions([
            [
                'content'=>'Edit',
                'icon'=>'create',
                "href"=> [
                    'path'=>'app_product_edit',
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
                    'path'=>'app_product_delete',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content'=>"Add product",
                'icon'=>'add-circle-outline',
                'href'=>[
                    'path'=>'app_product_new',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}