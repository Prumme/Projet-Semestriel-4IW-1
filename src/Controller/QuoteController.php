<?php

namespace App\Controller;

use App\Data\TemplatesList;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Quote;
use App\Entity\Company;
use App\Entity\User;
use App\Exception\URLSignedException;
use App\Form\QuoteType;
use App\Security\Voter\Attributes\UserVoterAttributes;
use App\Form\QuoteFilterType;
use App\Service\QuoteService;
use App\Service\URLSignedService;
use App\Table\QuoteTable;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\Attributes\QuoteVoterAttributes;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Data\QuoteSearch;

#[Route('/company/{company}/quote')]
class QuoteController extends AbstractController
{
    #[Route('/', name: 'app_quote_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(Request $request, QuoteRepository $quoteRepository, Company $company): Response
    {
        
        $filterData = new QuoteSearch();
        $filterForm  = $this->createForm(QuoteFilterType::class, $filterData, [
            'company' => $company,
        ]);
        $filterForm->handleRequest($request);
        $quotes = $quoteRepository->filtered($company, $filterData);

     
        // $quotes = $quoteRepository->findAllWithinCompany($company);
        
        $table = new QuoteTable($quotes, ["company" => $company]);



        return $this->render('quote/index.html.twig', [
            'table' => $table->createTable(),
            'form' => $filterForm->createView()
        ]);
    }

    #[Route('/new', name: 'app_quote_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function new(Request $request, EntityManagerInterface $entityManager, Company $company, QuoteService $quoteService): Response
    {
        $customer_id = !empty($_POST['quote']['customer']) ? $_POST['quote']['customer'] : $request->query->get('customer_id', null);
        $customer = null;
        if (isset($customer_id)) $customer = $entityManager->getRepository(Customer::class)->find($customer_id);

        $quote = new Quote();
        $quote->setEmitedAt(new \DateTime());
        $quote->setExpiredAt((new \DateTime())->modify('+1 month'));

        $products = $entityManager->getRepository(Product::class)->findAll();
        $form = $this->createForm(QuoteType::class, $quote, [
            'products' => array_map(fn ($product) => [
                'value' => json_encode($product->toArray()),
                'label' => $product->getName(),
            ], $products),
            'customer' => $customer,
            'company' => $company,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $quote->setOwner($this->getUser());
            $quoteService->cleanBillingRowsDiscounts($quote);
            $quoteService->cleanQuoteDiscounts($quote);
            $entityManager->persist($quote);
            $entityManager->flush();
            $this->addFlash('success', 'Quote created successfully');
            return $this->redirectToRoute('app_quote_edit', [
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
            'company' => $company,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($quote->getIsSigned()) throw new BadRequestHttpException("The quote has been signed, it cannot be edited");
            $quoteService->cleanBillingRowsDiscounts($quote);
            $quoteService->cleanQuoteDiscounts($quote);
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
    public function delete(Request $request, Company $company, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if($quote->getIsSigned()){
            $this->addFlash('error', 'You are not allowed to delete the quote '.$quote->getFormattedNumber().' because it has been signed');
        }  else if ($this->isCsrfTokenValid('delete' . $quote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();

            $this->addFlash('success', 'Quote deleted successfully');
        }
        return $this->redirectToRoute('app_quote_index', ['company' => $company->getId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('/delete', name: 'app_quote_mass_delete', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function massDelete(Request $request, Company $company, EntityManagerInterface $entityManager)
    {
        $selectedStr = $request->query->get('selected');
        if (!$selectedStr) return $this->redirectToRoute('app_quote_index', ['company' => $company->getId()]);

        $selectedIds = explode(',', $selectedStr);
        $CSRFToken = $request->query->get('_token');
        if ($this->isCsrfTokenValid('mass-action-token', $CSRFToken)) {
            foreach ($selectedIds as $quoteId) {
                $quote = $entityManager->getRepository(Quote::class)->find($quoteId);
                if ($this->isGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, $quote)){
                    dump($quote->getId());
                    if(!$quote->getIsSigned()) {
                    $entityManager->remove($quote);
                    $this->addFlash('success', 'Quote '.$quote->getFormattedNumber().'has been deleted successfully');
                    }else
                        $this->addFlash('error', 'You are not allowed to delete the quote '.$quote->getFormattedNumber().' because it has been signed');

                }
                else $this->addFlash('error', 'You are not allowed to delete the quote '. $quote->getFormattedNumber());
            }
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_quote_index', ['company' => $company->getId()]);
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
            $quote->setSignature($quoteSignature);
            $entityManager->flush();
            $this->addFlash('success', 'The quote has been signed');
            return $this->redirectToRoute('app_quote_preview', $previewURLSignedParams);
        }catch(\Exception $e){
            $this->addFlash('danger', 'The signature is not valid');
            return $this->redirectToRoute('app_quote_preview', $previewURLSignedParams);
        }
    }

    #[Route('/{id}/ask-signature', name: 'app_quote_ask_signature', methods: ['POST'])]
    #[IsGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, subject: 'quote')]
    public function askSignature(Quote $quote,Company $company,URLSignedService $urlSignedService): Response
    {
        if($quote->getIsSigned()) throw new BadRequestHttpException("The quote has been signed");
        $signedUrl = $urlSignedService->signURL('app_quote_preview', ['id' => $quote->getId(), 'company' => $company->getId()]);
        $urlSignedService->sendEmail($quote->getCustomer()->getEmail(), TemplatesList::NEW_QUOTE_OPENED,[
            "link"=> $_ENV['IP'] . $signedUrl,
            "name"=>$quote->getCustomer()->getFirstname(),
        ]);
        return $this->redirectToRoute('app_quote_edit', ['id' => $quote->getId(), 'company' => $company->getId()]);
    }
}
