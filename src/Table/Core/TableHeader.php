<?php

namespace App\Table\Core;

class TableHeader
{
    private $title;
    private $component;
    private $key;
    private $class;

    public function __construct($title,$component,$key,$class=null){
        $this->title = $title;
        $this->component = $component;
        $this->key = $key;
        $this->class= $class;
    }

    public function getKey(){
        return $this->key;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getComponent(){
        return $this->component;
    }

    public function createHeader(){
        return [
            'title' => $this->getTitle(),
            'component' => $this->getComponent(),
            'key' => $this->getKey(),
            'class' => $this->class,
        ];
    }
}