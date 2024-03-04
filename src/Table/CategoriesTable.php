<?php

namespace App\Table;

use App\Security\Voter\Attributes\CategoryVoterAttributes;
use App\Table\Core\Table;

class CategoriesTable extends Table{
    protected array $neededData = ['company','security'];
    protected function buildTable() : void
    {
        $company = $this->data['company'];
        $security = $this->data['security'];

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
                'title'=>"Number of products",
                'key'=>'NumberOfProducts',
            ]
        ]);

        $this->setItemsActions([
            [
                'content'=>'Edit',
                'icon'=>'create',
                "href"=> [
                    'path'=>'app_category_edit',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
            [
                'content'=>'Delete',
                'icon'=>'trash',
                'visible'=> fn($item) => $security->isGranted(CategoryVoterAttributes::CAN_DELETE_CATEGORY,$item),
                "href"=> [
                    'csrf'=> fn($item) => 'delete' . $item->getId(),
                    'method'=>'post',
                    'path'=>'app_category_delete',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ],
        ]);


        $this->setActions([
            [
                'content'=>"Add Category",
                'icon'=>'add-circle-outline',
                'href'=>[
                    'path'=>'app_category_new',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}