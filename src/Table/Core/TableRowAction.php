<?php

namespace App\Table\Core;

class TableRowAction
{
    private string $content;
    private string $icon;
    private array $href;
    private $item;
    public function __construct(string $content,string $icon,array $href,$item){
        $this->content = $content;
        $this->icon = $icon;
        $this->href = $href;
        $this->item = $item;
    }

    public function getContent(){
        return $this->content;
    }

    public function getIcon(){
        return $this->icon;
    }


    private function callIfCallable($el){
        if(is_callable($el)){
            return $el($this->item);
        }else
            return $el;
    }

    public function getHref(){
        return [
            'csrf' => isset($this->href['csrf']) ? $this->callIfCallable($this->href['csrf']) :  false,
            'method' => $this->href['method'] ?? 'get',
            'path' => $this->href['path'],
            'params' => array_map(function($param){
                    return $this->callIfCallable($param);
                },$this->href['params'])
        ];
    }

    public function createRowAction(){
        return [
            'content' => $this->getContent(),
            'icon' => $this->getIcon(),
            'href' => $this->getHref(),
        ];
    }
}