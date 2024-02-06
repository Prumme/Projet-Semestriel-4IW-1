<?php

namespace App\Service;

use App\Data\TemplatesList;
use App\Exception\URLSignedException;
use App\Model\URLSigned;
use mysql_xdevapi\Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class URLSignedService
{
    public function __construct(private UrlGeneratorInterface $router, private EmailService $emailService)
    {
    }
    public function generateToken($tokenData): string
    {
        return hash_hmac("sha256",$tokenData,$_ENV['APP_SECRET']);
    }

    public function encodeData($tokenData): string
    {
        try{
            return bin2hex(json_encode($tokenData));
        }catch (Exception $e) {
            throw new URLSignedException(URLSignedException::INVALID);
        }
    }
    public function decodeData($dataEncoded){
        try{
            return json_decode(hex2bin($dataEncoded),false);
        }catch (Exception $e) {
            throw new URLSignedException(URLSignedException::INVALID);
        }
    }

    public function signURL($routeName,$routeParams,$tokenData = [],$expireDuration = '+12 hours'): string
    {
        $expiredAt = (new \DateTime())->modify($expireDuration);
        $dataEncoded = $this->encodeData(['params'=>$routeParams,...$tokenData,'e'=>$expiredAt->getTimestamp()]);
        $token = $this->generateToken($dataEncoded);
        return $this->router->generate($routeName, [...$routeParams,'t' => $token, 'd' => $dataEncoded]);
    }

    public function verifyToken($token,$dataEncoded): bool
    {
        try{
            $validToken = $this->generateToken($dataEncoded);
            return $validToken == $token;
        }catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws URLSignedException
     */

    /**
     * @throws URLSignedException
     */
    public function verifyURL($request, $checkParams = true) : URLSigned{
        $token = $request->query->get('t',null);
        $data = $request->query->get('d',null);
        if(!$token || !$data) throw new URLSignedException(URLSignedException::INVALID);
        $dataDecoded= $this->decodeData($data);
        if(!isset($dataDecoded) || !isset($dataDecoded->params)) throw new URLSignedException(URLSignedException::INVALID);
        $urlSigned = new URLSigned($request,$token,$dataDecoded);
        if(!$this->verifyToken($token,$data)) throw new URLSignedException(URLSignedException::INVALID,$urlSigned);
        if($checkParams && !$this->verifyParams($request,$dataDecoded)) throw new URLSignedException(URLSignedException::INVALID,$urlSigned);
        if($urlSigned->hasExpired()) throw new URLSignedException(URLSignedException::EXPIRED,$urlSigned);
        return $urlSigned;
    }

    public function verifyParams($request, $dataDecoded): bool
    {
        $requestParams = $request->attributes->get('_route_params');
        foreach ($dataDecoded->params as $key=>$value)
            if(!isset($requestParams[$key]) || $requestParams[$key] != $value) return false;
        return true;
    }

    public function isURLSigned($request): bool
    {
        try{
            $this->verifyURL($request,false);
            return true;
        }catch(URLSignedException $e){
            if($e->hasExpired()) return true;
            return false;
        }
    }


    public function sendEmail($to,$urlSigned){
        // @TODO en attente de la template
        /*$templateId = TemplatesList::FORGET_PASSWORD;
        $templateVariables = [
            'link'=>$urlSigned,
        ];
        $this->emailService->sendEmailWithTemplate($to, $templateId, $templateVariables);*/
    }
}