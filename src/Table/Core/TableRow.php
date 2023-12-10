<?php

namespace App\Table\Core;

class TableRow
{
    private array $headers;
    private $item;
    private $actions;

    public function __construct( $headers, $item, $actions)
    {
        $this->headers = $headers;
        $this->item = $item;
        $this->actions = $actions;
    }
    public function getValue(TableHeader $header)
    {
        if($header->getKey() === 'actions'){
            return $this->getActions();
        }else {
            $key = $header->getKey();
            $getter = 'get'.ucfirst($key);
            return $this->item->$getter();
        }
    }
    public function getActions() : array
    {
        return array_map(function($action){
            $tableRowAction = new TableRowAction($action['content'],$action['icon'],$action['href'],$this->item);
            return $tableRowAction->createRowAction();
        },$this->actions);
    }

    public function getCells(): array
    {
        return array_map(function($header){
            return [
                'component' => $header->getComponent(),
                'value' => $this->getValue($header),
                'header' => $header->createHeader(),
            ];
        },$this->headers);
    }

    public function createRow(){
        return [
            'cells' => $this->getCells(),
            'item' => $this->item,
        ];
    }
}