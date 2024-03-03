<?php

namespace App\Exception;

use App\Model\URLSigned;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class URLSignedException extends NotFoundHttpException
{
    const EXPIRED = 'URL Expired';
    const INVALID = 'Invalid URL';

    private URLSigned $urlSigned;
    public function __construct($message, URLSigned $urlSigned = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);
        if($urlSigned)
            $this->urlSigned = $urlSigned;
    }

    public function hasExpired(){
        return $this->getMessage() == self::EXPIRED;
    }

    public function getUrlSigned(): URLSigned
    {
        return $this->urlSigned;
    }
}