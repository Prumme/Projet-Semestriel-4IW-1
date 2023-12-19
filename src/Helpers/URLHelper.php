<?php

namespace App\Helpers;



class URL {

    private string $base;

    public function __construct(string $base)
    {
        $this->base = $base;
    }

    public function generateUrl(string $path, array $params): string {
        return $this->base.$path.'?'.http_build_query($params);
    }


}