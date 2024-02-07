<?php

namespace App\Service;

use App\Entity\BillingRow;
use App\Entity\Quote;
use App\Entity\QuoteSignature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuoteService
{
    public function __construct(private EntityManagerInterface $entityManager, private ValidatorInterface $validator)
    {
    }

    public function syncBillingRows(Quote $quote): void
    {
        $current_billing_rows =  $this->entityManager->getRepository(BillingRow::class)->findBy(['quote_id' => $quote->getId()]);
        foreach ($quote->getBillingRows() as $billingRow) {
            if(!$billingRow->getQuoteId())
                $billingRow->setQuoteId($quote);
        }
        foreach ($current_billing_rows as $current_billing_row) {
            if (!$quote->getBillingRows()->contains($current_billing_row)) {
                $this->entityManager->remove($current_billing_row);
            }
        }
        $this->entityManager->flush();
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