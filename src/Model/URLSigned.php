<?php

namespace App\Model;


use App\Exception\URLSignedException;
use Symfony\Component\HttpFoundation\Request;

class URLSigned
{
    public function __construct(
        private Request $request,
        private string $token,
        private object $data,
    ) {}

    public function hasExpired() : bool
    {
        if (!isset($this->data->e)) throw new URLSignedException(URLSignedException::INVALID,$this);
        $expiredAt = (new \DateTime())->setTimestamp($this->data->e);
        if ($expiredAt < new \DateTime()) return true;
        return false;
    }

    public function getTempalte(){
        return 'bundles/UrlSigned/expired.html.twig';
    }

    public function getResentLink(): string
    {
        return $this->request->getUri() . '&r=1';
    }

    public function isResent(): bool
    {
        return $this->request->query->get('r',0) == 1;
    }

}