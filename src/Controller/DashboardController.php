<?php

namespace App\Controller;

use App\Entity\BillingRow;
use App\Repository\BillingRowRepository;
use App\Security\AuthentificableRoles;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\BillingAddressRepository;
use App\Repository\CustomerRepository;
use App\Repository\QuoteRepository;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function index(BillingRowRepository $billingRowRepository, QuoteRepository $quoteRepository, CustomerRepository $customerRepository): Response
    {
        $monthlyNetIncome = $billingRowRepository->monthlyNetIncome($this->getUser()->getCompany()->getId());

        $monthlyQuotesCount = $quoteRepository->monthlyQuotesCount($this->getUser()->getCompany()->getId());

        $countCustomer = $customerRepository->countAllWithingCompany($this->getUser()->getCompany());

        $avgQuotesPrice = $quoteRepository->getAvgQuotePrice($this->getUser()->getCompany()->getId());

        $topFiveQuotes = $quoteRepository->topFiveQuotes($this->getUser()->getCompany()->getId());

        $monthlyQuoteValue = $quoteRepository->getAllQuoteValuesByDay($this->getUser()->getCompany()->getId());

        $bestSellers = $billingRowRepository->getBestSellers($this->getUser()->getCompany()->getId());

        return $this->render('dashboard/index.html.twig',
            [
                'company' => $this->getUser()->getCompany()->getId(),
                'monthlyNetIncome' => $monthlyNetIncome['thisMonth'],
                'IncomeEvolutionRate' => $monthlyNetIncome['evolution_rate_percentage'],
                'monthlyQuotesCount' => $monthlyQuotesCount['thisMonth'],
                'QuotesCountEvolutionRate' => $monthlyQuotesCount['evolution_rate_percentage'],
                'countCustomer' => $countCustomer,
                'monthlyAvgQuotePrice' => $avgQuotesPrice['avg_quote_price'],
                'AvgQuotePriceEvolutionRate' => $avgQuotesPrice['evolution_rate_percentage'],
                'topFiveQuotes' => $topFiveQuotes,
                'monthlyQuoteValue' => json_encode($monthlyQuoteValue),
                'bestSellers' => json_encode($bestSellers)
            ]);
    }
}
