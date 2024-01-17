<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Company;
use App\Form\Quote1Type;
use App\Table\QuoteTable;
use App\Repository\QuoteRepository;
use App\Security\AuthentificableRoles;
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
    public function new(Request $request, EntityManagerInterface $entityManager, Company $company): Response
    {
        $quote = new Quote();
        $form = $this->createForm(Quote1Type::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $entityManager->flush();

            $this->addFlash('success', 'Quote created successfully');

            return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quote_show', methods: ['GET'])]
    #[IsGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, subject: 'quote')]
    public function show(Quote $quote): Response
    {
        return $this->render('quote/show.html.twig', [
            'quote' => $quote,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quote_edit', methods: ['GET', 'POST'])]
    #[IsGranted(QuoteVoterAttributes::CAN_MANAGE_QUOTE, subject: 'quote')]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Quote1Type::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Quote edited successfully');

            return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quote/edit.html.twig', [
            'quote' => $quote,
            'form' => $form,
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
}
