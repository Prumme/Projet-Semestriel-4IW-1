<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Quote;
use App\Entity\Company;
use App\Entity\Invoice;
use App\Security\AuthentificableRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('q');

        $isSuperAdmin = $this->getUser()->hasRole(AuthentificableRoles::ROLE_SUPER_ADMIN);

        // COMPANY
        $companies = $isSuperAdmin ? $entityManager
        ->getRepository(Company::class)
        ->createQueryBuilder('c')
        ->where('c.name LIKE :query')
        ->setParameter('query', '%'.$query.'%')
        ->getQuery()
        ->getResult() : [];

        // USERS
        $userQuery = $entityManager
        ->getRepository(User::class)
        ->createQueryBuilder('u')
        ->where('LOWER(u.firstname) LIKE LOWER(:query) OR LOWER(u.lastname) LIKE LOWER(:query) OR LOWER(u.email) LIKE LOWER(:query)')
        ->setParameter('query', '%'.$query.'%');

        if (!$isSuperAdmin) {
            $userQuery->andWhere('u.company = :company')
            ->setParameter('company', $this->getUser()->getCompany());
        } 
        $users = $userQuery->getQuery()->getResult();

        // INVOICES
        $invoiceQuery = $entityManager
        ->getRepository(Invoice::class)
        ->createQueryBuilder('i')
        ->where('LOWER(i.number) LIKE LOWER(:query)')
        ->setParameter('query', '%'.$query.'%');

        if (!$isSuperAdmin) {
            $invoiceQuery
                ->join('i.quote', 'q')
                ->join('q.owner', 'o')
                ->andWhere('o.company = :company')
            ->setParameter('company', $this->getUser()->getCompany());
        }
        $invoices = $invoiceQuery->getQuery()->getResult();

        // QUOTES
        $quoteQuery = $entityManager
        ->getRepository(Quote::class)
        ->createQueryBuilder('q');
        
        if (!$isSuperAdmin) {
            $quoteQuery
                ->join('q.owner', 'o')
                ->andWhere('o.company = :company')
            ->setParameter('company', $this->getUser()->getCompany());
        }
        $quotes = $quoteQuery->getQuery()->getResult();
        $quotes = array_filter($quotes, fn($quote) => strpos($quote->getFormatedNumber(), $query) !== false);

        $search = ['users' => [], 'companies' => [], 'invoices' => [], 'quotes' => []];

        // USER
        foreach ($users as $user) {
            $totalString = $user->getFirstname() . $user->getLastname() . $user->getEmail();
            $totalString = strtolower($totalString);
            $query = strtolower($query);
            $user->matchingRate = similar_text($totalString, $query) / strlen($totalString);
            $search['users'][] = [
                'label' => $user->getIdentity(),
                'email' => $user->getEmail(),
                'type' => 'user',
                'matchingRate' => $user->matchingRate,
                'url' => $this->generateUrl('app_company_user_edit', ['id' => $user->getId(), 'company' => $user->getCompany()->getId()])
            ];
        }

        // COMPANY
        foreach ($companies as $company) {
            $totalString = $company->getName();
            $totalString = strtolower($totalString);
            $query = strtolower($query);
            $company->matchingRate = similar_text($totalString, $query) / strlen($totalString);
            $search['companies'][] = [
                'label' => $company->getName(),
                'type' => 'company',
                'matchingRate' => $company->matchingRate,
                'url' => $this->generateUrl('app_company_edit', ['id' => $company->getId()])
            ];
        }

        // INVOICE
        foreach ($invoices as $invoice) {
            $totalString = $invoice->getInvoiceNumber();
            $totalString = strtolower($totalString);
            $query = strtolower($query);
            $invoice->matchingRate = similar_text($totalString, $query) / strlen($totalString);
            $search['invoices'][] = [
                'label' => $invoice->getInvoiceNumber(),
                'type' => 'invoice',
                'matchingRate' => $invoice->matchingRate,
                'url' => $this->generateUrl('app_invoice_edit', ['invoice' => $invoice->getId(), 'company' => $invoice->getQuote()->getOwner()->getCompany()->getId()]),
            ];
        }

        // QUOTES
        foreach ($quotes as $quote) {
            $totalString = $quote->getFormatedNumber();
            $totalString = strtolower($totalString);
            $query = strtolower($query);
            $quote->matchingRate = similar_text($totalString, $query) / strlen($totalString);
            $search['quotes'][] = [
                'label' => $quote->getFormatedNumber(),
                'type' => 'quote',
                'matchingRate' => $quote->matchingRate,
                'url' => $this->generateUrl('app_quote_edit', ['id' => $quote->getId(), 'company' => $quote->getOwner()->getCompany()->getId()])
            ];
        }

        usort($search['users'], function($a, $b) {
            return $b['matchingRate'] <=> $a['matchingRate'];
        });
        
        usort($search['companies'], function($a, $b) {
            return $b['matchingRate'] <=> $a['matchingRate'];
        });

        usort($search['invoices'], function($a, $b) {
            return $b['matchingRate'] <=> $a['matchingRate'];
        });

        usort($search['quotes'], function($a, $b) {
            return $b['matchingRate'] <=> $a['matchingRate'];
        });

        return $this->render('search/index.html.twig', [
            'results' => $search,
            'isSuperAdmin' => $isSuperAdmin,
        ]);

    }
}
