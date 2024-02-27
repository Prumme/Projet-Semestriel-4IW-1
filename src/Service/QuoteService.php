<?php

namespace App\Service;

use App\Entity\Quote;
use App\Entity\QuoteSignature;
use Symfony\Component\HttpFoundation\Request;

class QuoteService
{
    public function __construct(){}

    public function cleanBillingRowsDiscounts(Quote &$quote): void
    {
        foreach ($quote->getBillingRows() as $billingRow) {
            if($billingRow->getDiscount()) {
                $discount = $billingRow->getDiscount();
                if(empty($discount->getValue())) $billingRow->setDiscount(null);
            }
        }
    }

    public function cleanQuoteDiscounts(Quote &$quote): void
    {
        foreach ($quote->getDiscounts() as $quoteDiscount) {
            if($quoteDiscount->getDiscount()) {
                $discount = $quoteDiscount->getDiscount();
                if(empty($discount->getValue())) {
                    $quote->removeDiscount($quoteDiscount);
                }
            }else $quote->removeDiscount($quoteDiscount);
        }
    }

    public function checkIfValidSignatureDataURI($signatureURI = null): bool
    {
        if (!$signatureURI) return false;
        $imageInfo = getimagesizefromstring(base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureURI)));
        if($imageInfo === false || $imageInfo['mime'] != 'image/png') return false;
        return true;
    }

    public function createQuoteSignature(Request $request): ?QuoteSignature
    {
        $signatureURI = $request->request->get('signature',null);
        if(!$this->checkIfValidSignatureDataURI($signatureURI)) return null;
        $quoteSignature = new QuoteSignature();
        $quoteSignature->setDataBase64URI($signatureURI);
        $quoteSignature->setSignedAt(new \DateTimeImmutable());
        $quoteSignature->setSignedBy($request->request->get('signedBy',null));
        $quoteSignature->setHasBeenAgreed($request->request->get('hasBeenAgreed',false));
        $errors = $this->validator->validate($quoteSignature);
        if (count($errors) > 0) return null;
        return $quoteSignature;
    }
}