<?php

namespace App\Controller;

use App\Data\InvoiceSearch;
use App\Exception\URLSignedException;
use App\Form\InvoiceFilterType;
use App\Helper\URL;
use App\Entity\Quote;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Data\TemplatesList;
use App\Security\Voter\Attributes\QuoteVoterAttributes;
use App\Service\URLSignedService;
use App\Table\InvoiceTable;
use App\Service\EmailService;
use App\Repository\InvoiceRepository;
use App\Service\InvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/company/{company}/invoice')]
class InvoiceController extends AbstractController
{
    private $sendinblueService;
    private $urlHelper;

    public function __construct(EmailService $sendinblueService, URL $urlHelper)
    {
        $this->sendinblueService = $sendinblueService;
        $this->urlHelper = $urlHelper;
    }
    
    #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository, Company $company): Response
    {
        $filterData = new InvoiceSearch();
        $filterForm  = $this->createForm(InvoiceFilterType::class, $filterData, [
            'company' => $company,
        ]);
        $filterForm->handleRequest($request);
        $invoices = $invoiceRepository->filtered($company, $filterData);
        $table = new InvoiceTable($invoices, ["company" => $company]);
        return $this->render('invoice/index.html.twig', [
            'table' => $table->createTable(),
            'filterForm' => $filterForm->createView(),
        ]);
        
    }

    #[Route('/generate-invoice/{quote}', name: 'app_generate_invoice', methods: ['POST'])]
    public function generateInvoice(EntityManagerInterface $entityManager, Quote $quote, Company $company, InvoiceService $invoiceService, URLSignedService $urlSignedService)
    {
        $quote = $entityManager->getRepository(Quote::class)->find($quote);

        if (!$quote) {
            throw $this->createNotFoundException('The quote does not exist.');
        }

        //cancel the previous invoice
        $previousInvoices = $quote->getInvoices();
        foreach ($previousInvoices as $previousInvoice) {
            $previousInvoice->setStatus(Invoice::STATUS_CANCELLED);
        }

        $invoice = new Invoice();
        $invoice->setQuote($quote);
        $invoice->setInvoiceNumber($invoiceService, $quote);
        $invoice->setStatus(Invoice::STATUS_AWAITING_PAYMENT);
        $invoice->setEmittedAt(new \DateTime());
        $invoice->setExpiredAt((new \DateTime())->modify('+1 month'));

        $entityManager->persist($invoice);
        $entityManager->flush();

        // EMAIL CONFIGURATION
        $to = $quote->getCustomer()->getEmail();
        $templateId = TemplatesList::NEW_INVOICE;
        $user = $quote->getCustomer()->getIdentity();
        $signedUrl = $urlSignedService->signURL('app_invoice_preview', ['invoice' => $invoice->getId(), 'company' => $company->getId()]);
        $pay_link = $urlSignedService->signURL('app_invoice_paid', ['invoice' => $invoice->getId(), 'company' => $company->getId()]);
        
        $templateVariables = [
            'name' => $user,
            'link' => $_ENV['IP'] . $signedUrl,
            'pay_link' => $_ENV['IP'] . $pay_link, 
        ];

        $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

        $this->addFlash('success', 'Invoice created successfully!');

        return $this->redirectToRoute('app_invoice_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{invoice}/paid', name: 'app_invoice_paid', methods: ['GET'])]
    public function paid(Request $request, Invoice $invoice, Company $company, EntityManagerInterface $entityManager, URLSignedService $urlSignedService): Response
    {
        if($invoice->getStatus() === Invoice::STATUS_CANCELLED) throw $this->createNotFoundException('The invoice has been cancelled.');
        if($invoice->getStatus() === Invoice::STATUS_PAID) throw $this->createNotFoundException('The invoice has already been paid.');

        $invoice->setStatus(Invoice::STATUS_PAID);
        $quote = $invoice->getQuote();
        $entityManager->flush();
        
        try {
            $urlSignedService->verifyURL($request);
        }catch (URLSignedException $e){
            if(!$e->hasExpired()) throw $e;
            $urlSigned = $e->getUrlSigned();
            $customerEmail = $quote->getCustomer()->getEmail();
            $renderData = [
                'urlSigned' => $urlSigned,
                'email' => $customerEmail,
            ];
            if ($urlSigned->isResent()) {
                $newUrl = $urlSignedService->signURL('app_quote_paid', ['id' => $invoice->getId(), 'company' => $company->getId()], '+1 hours');
                $urlSignedService->sendEmail($quote->getCustomer()->getEmail(), TemplatesList::SIGNED_URL_EXPIRED,[
                    "link"=> $_ENV['IP'] . $newUrl,
                    "name"=>$quote->getCustomer()->getFirstname(),
                ]);
                $renderData = [
                    ...$renderData,
                    'sended' => true,
                ];
            }
            return $this->render($urlSigned->getTemplate(), $renderData);
        }

        $urlSigned = $urlSignedService->signURL('app_invoice_preview', ['invoice' => $invoice->getId(), 'company' => $company->getId()]);

        return $this->render('invoice/paid.html.twig', [
            'previewSignedURL' => $urlSigned,
        ]);
    }

    #[Route('/{invoice}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,Company $company, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Invoice updated successfully!');
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'company'=> $company,
            'quote'=> $invoice->getQuote(),
            'form' => $form,
        ]);
    }

    #[Route('/{invoice}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'Invoice deleted successfully!');

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{invoice}/preview', name: 'app_invoice_preview', methods: ['GET'])]
    public function preview(Request $request, Company $company,Invoice $invoice,  URLSignedService $urlSignedService): Response
    {
        if($invoice->getStatus() === Invoice::STATUS_CANCELLED) throw $this->createNotFoundException('The invoice has been cancelled.');
        $quote = $invoice->getQuote();
        if(!$urlSignedService->isURLSigned($request)) $this->denyAccessUnlessGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE,$quote);
        else{
            try {
                $urlSignedService->verifyURL($request);
            }catch (URLSignedException $e){
                if(!$e->hasExpired()) throw $e;
                $urlSigned = $e->getUrlSigned();
                $customerEmail = $quote->getCustomer()->getEmail();
                $renderData = [
                    'urlSigned' => $urlSigned,
                    'email' => $customerEmail,
                ];
                if ($urlSigned->isResent()) {
                    $newUrl = $urlSignedService->signURL('app_quote_preview', ['id' => $invoice->getId(), 'company' => $company->getId()]);
                    $urlSignedService->sendEmail($quote->getCustomer()->getEmail(), TemplatesList::SIGNED_URL_EXPIRED,[
                        "link"=> $_ENV['IP'] . $newUrl,
                        "name"=>$quote->getCustomer()->getFirstname(),
                    ]);
                    $renderData = [
                        ...$renderData,
                        'sended' => true,
                    ];
                }
                return $this->render($urlSigned->getTemplate(), $renderData);
            }
        }

        return $this->render('invoice/preview.html.twig', [
            'invoice'=> $invoice,
            'quote' => $quote,
            'company' => $company,
            'embeded' => $request->query->get('embeded',false),
        ]);
    }

}
