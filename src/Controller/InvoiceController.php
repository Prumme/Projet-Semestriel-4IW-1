<?php

namespace App\Controller;

use App\Helper\URL;
use App\Entity\Quote;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Data\TemplatesList;
use App\Table\InvoiceTable;
use App\Service\EmailService;
use App\Repository\QuoteRepository;
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
    public function index(QuoteRepository $quoteRepository, Company $company): Response
    {
        // return all invoices of the company
        $invoices = $quoteRepository->findAllWithinCompany($company);
        $table = new InvoiceTable($invoices, ["company" => $company]);
        return $this->render('invoice/index.html.twig', [
            'table' => $table->createTable(),
        ]);
        
    }

    #[Route('/generate-invoice/{quote}', name: 'app_generate_invoice', methods: ['POST'])]
    public function generateInvoice(EntityManagerInterface $entityManager, Quote $quote, Company $company, InvoiceService $invoiceService)
    {
        $quote = $entityManager->getRepository(Quote::class)->find($quote);

        if (!$quote) {
            throw $this->createNotFoundException('The quote does not exist.');
        }

        $invoice = new Invoice();
        $invoice->setQuote($quote);
        $invoice->setInvoiceNumber($invoiceService, $quote);
        $invoice->setStatus('awaiting_payment');
        $invoice->setEmittedAt(new \DateTime());
        $invoice->setExpiredAt((new \DateTime())->modify('+1 month'));

        $entityManager->persist($invoice);
        $entityManager->flush();

        // EMAIL CONFIGURATION
        $to = $quote->getCustomer()->getEmail();
        $templateId = TemplatesList::NEW_INVOICE;
        $user = $quote->getCustomer()->getIdentity();
        $url = $this->urlHelper->generateUrl('app_invoice_show', ['invoice' => $invoice->getId(), 'company' => $company->getId()]);

        $templateVariables = [
            'name' => $user,
            'link' => $url,
        ];

        $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

        $this->addFlash('success', 'Invoice created successfully!');

        return $this->redirectToRoute('app_invoice_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
