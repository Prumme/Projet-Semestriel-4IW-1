<?php

namespace App\Table\Core;

abstract class Table{
    private array $headers;
    private  $items;
    private  array $actions;
    private  array $itemsActions;
    private array $groupedActions;
    protected  array $data;
    protected array $neededData = [];
    public function __construct($items, $data = []){
        $this->items = $items;
        $this->setData($data);
        $this->buildTable();
    }

    public function getHeaders() : array
    {
        $headers =  array_map(function($header){
            return new TableHeader($header['title'],$header['component'] ?? null,$header['key'] ?? null,$header['value'] ?? null);
        },$this->headers);
        if(count($this->itemsActions) > 0){
            $headers[] = new TableHeader('Actions','components/tables/table.actions.html.twig','actions','text-center');
        }
        return $headers;
    }
    public function getRows() : array
    {
        return array_map(function($item){
            $tableRow = new TableRow($this->getHeaders(),$item,$this->itemsActions);
            return $tableRow->createRow();
        },$this->items);
    }

    final public function createTable() : array
    {
        $headers = array_map(function($header){
            return $header->createHeader();
        },$this->getHeaders());
        return [
            'headers' => $headers,
            'rows' => $this->getRows(),
            'groupedActions' => $this->groupedActions ?? null,
            'actions'=> $this->actions ?? null,
        ];
    }
    public function setHeaders($headers) : void
    {
        $this->headers = $headers;
    }
    public function setActions($actions) : void
    {
        $this->actions = $actions;
    }
    public function setItemsActions($itemActions) : void
    {
        $this->itemsActions = $itemActions;
    }

    public function setGroupedActions($groupedActions) : void
    {
        $this->groupedActions = $groupedActions;
    }

    public function setData($data) : void
    {
        foreach($this->neededData as $key)
            if(!array_key_exists($key,$data)) throw new \Exception("[TABLE] Data key $key is missing");
        $this->data = $data;
    }
    abstract protected function buildTable() : void;
}