<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Quote;
use App\Entity\Company;
use App\Exception\URLSignedException;
use App\Form\QuoteType;
use App\Service\QuoteService;
use App\Service\URLSignedService;
use App\Table\QuoteTable;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\Attributes\QuoteVoterAttributes;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/company/{company}/quote')]
// TODO : Add /company/{company}/quote to the route | All controllers add Company $company ONLY WHEN NEEDED
class QuoteController extends AbstractController
{
    #[Route('/', name: 'app_quote_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(QuoteRepository $quoteRepository, Company $company): Response
    {
        $quotes = $quoteRepository->findAll();
        $table = new QuoteTable($quotes, ["company" => $company]);
        return $this->render('quote/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_quote_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function new(Request $request, EntityManagerInterface $entityManager, Company $company,QuoteService $quoteService): Response
    {
        $customer_id = !empty($_POST['quote']['customer']) ? $_POST['quote']['customer'] : $request->query->get('customer_id',null);
        $customer = null;
        if(isset($customer_id)) $customer = $entityManager->getRepository(Customer::class)->find($customer_id);

        $quote = new Quote();

        $products = $entityManager->getRepository(Product::class)->findAll();
        $form = $this->createForm(QuoteType::class, $quote,[
            'products' => array_map(fn($product) => [
                'value' => json_encode($product->toArray()),
                'label' => $product->getName(),
            ], $products),
            'customer' => $customer,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $quoteService->syncBillingRows($quote);

            $entityManager->flush();
            $this->addFlash('success', 'Quote created successfully');
            return $this->redirectToRoute('app_quote_edit',[
                'id' => $quote->getId(),
                'company' => $company->getId(),
            ]);
        }

        return $this->render('quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quote_edit', methods: ['GET', 'POST'])]
    #[IsGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, subject: 'quote')]
    public function edit(Request $request,Company $company, Quote $quote, EntityManagerInterface $entityManager,QuoteService $quoteService): Response
    {
        $customer_id = !empty($_POST['quote']['customer']) ? $_POST['quote']['customer'] : $request->query->get('customer_id',null);
        $customer =  $quote->getCustomer();
        if(isset($customer_id)){
            $customer = $entityManager->getRepository(Customer::class)->find($customer_id);
        }

        $products = $entityManager->getRepository(Product::class)->findAll();
        $form = $this->createForm(QuoteType::class, $quote,[
            'products' => array_map(fn($product) => [
                'value' => json_encode($product->toArray()),
                'label' => $product->getName(),
            ], $products),
            'customer' => $customer,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quoteService->syncBillingRows($quote);
            $entityManager->flush();
            $this->addFlash('success', 'Quote edited successfully');
        }

        return $this->render('quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_quote_delete', methods: ['POST'])]
    #[IsGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, subject: 'quote')]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $quote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();

            $this->addFlash('success', 'Quote deleted successfully');
        }
        return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/preview', name: 'app_quote_preview', methods: ['GET'])]
    public function preview(Request $request, Quote $quote,Company $company, URLSignedService $urlSignedService): Response
    {
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
                    $newUrl = $urlSignedService->signURL('app_quote_preview', ['id' => $quote->getId(), 'company' => $company->getId()]);
                    $urlSignedService->sendEmail($customerEmail, $newUrl);
                    $renderData = [
                        ...$renderData,
                        'sended' => true,
                    ];
                }
                return $this->render($urlSigned->getTemplate(), $renderData);
            }
        }
        $signPostURLsigned = $urlSignedService->signURL('app_quote_sign', ['id' => $quote->getId(), 'company' => $company->getId()],"+1 hour");
        return $this->render('quote/preview.html.twig', [
            'quote' => $quote,
            'company' => $company,
            'signPostURLsigned' => $signPostURLsigned,
            'embeded' => $request->query->get('embeded',false),
        ]);
    }

    #[Route('/{id}/sign', name: 'app_quote_sign', methods: ['POST'])]
    public function sign(Request $request, Quote $quote,Company $company, EntityManagerInterface $entityManager,QuoteService $quoteService,URLSignedService $urlSignedService): Response
    {
        $urlSignedService->verifyURL($request);
        $previewURLSignedParams = $urlSignedService->signURL('app_quote_preview', ['id' => $quote->getId(), 'company' => $company->getId()],'+1 hours',[],true);
        try{
            $quoteSignature = $quoteService->createQuoteSignature($request);
            if(!$quoteSignature) throw new \Exception("Invalid signature");
            $entityManager->persist($quoteSignature);;
            $quote->setHasBeenSigned(true);
            $quote->setSignature($quoteSignature);
            $entityManager->flush();
            $this->addFlash('success', 'The quote has been signed');
            return $this->redirectToRoute('app_quote_preview', $previewURLSignedParams);
        }catch(\Exception $e){
            $this->addFlash('danger', 'The signature is not valid');
            return $this->redirectToRoute('app_quote_preview', $previewURLSignedParams);
        }
    }

    #[Route('/{id}/send', name: 'app_quote_send', methods: ['GET'])]
    public function generatePreviewURL(Quote $quote,Company $company,URLSignedService $urlSignedService): string
    {
        $url = $urlSignedService->signURL('app_quote_preview', ['id' => $quote->getId(), 'company' => $company->getId()]);
        die("<a href=\"$url\">test</a>");
    }
}
