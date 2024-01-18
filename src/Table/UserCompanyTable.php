<?php

namespace App\Table;

use App\Table\Core\Table;

class UserCompanyTable extends Table{
    protected array $neededData = ['company'];
    protected function buildTable() : void
    {
        $company = $this->data['company'];

        $this->setHeaders([
            [
                'title' => 'Identity',
                'component' => 'company_user/identity_cell.html.twig',
                'key' => 'email',
            ],
            [
                'title' => 'Roles',
                'component' => 'company_user/roles_cell.html.twig',
                'key' => 'roles',
            ]
        ]);

        $this->setItemsActions([
            [
                'content' => "Send onboarding email",
                'icon' => 'mail',
                'visible' => fn($item) => !$item->isActivate(),
                "href"=> [
                    'path'=>'app_company_user_send_activate',
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
                    'path'=>'app_company_user_edit',
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
                    'path'=>'app_company_user_delete',
                    'params'=>[
                        'id'=> fn($item)=>$item->getId(),
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);

        $this->setGroupedActions([
            [
                'content'=>"delete",
                'icon'=>'trash',
                'href'=>[
                    'path'=>'app_company_user_mass_delete',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);

        $this->setActions([
            [
                'content'=>"Add user",
                'icon'=>'add-circle-outline',
                'href'=>[
                    'path'=>'app_company_user_new',
                    'params'=>[
                        'company'=> $company->getId(),
                    ]
                ],
            ]
        ]);
    }
}