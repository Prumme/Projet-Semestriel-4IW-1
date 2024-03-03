<?php

namespace App\Table\Core;

class TableRowAction
{
    private string $content;
    private string $icon;
    private array $href;
    private $item;
    private $visible;
    
    public function __construct(string $content,string $icon,array $href, $visible,$item){
        $this->content = $content;
        $this->icon = $icon;
        $this->href = $href;
        $this->item = $item;
        $this->visible = $visible;
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
            'visible' => $this->callIfCallable($this->visible),
            'content' => $this->getContent(),
            'icon' => $this->getIcon(),
            'href' => $this->getHref(),
        ];
    }
}