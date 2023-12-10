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

    public function getHref(){
        $item = $this->item;
        return [
            'path' => $this->href['path'],
            'params' => array_map(function($param) use ($item){
                    if(is_callable($param)){
                        return $param($item);
                    }else
                        return $param;
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