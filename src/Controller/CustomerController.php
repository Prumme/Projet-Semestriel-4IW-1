<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use App\Security\Voter\Attributes\CustomerVoterAttributes;
use App\Table\CustomersTable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/{company}/customer')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(Company $company, CustomerRepository $customerRepository): Response
    {
        $customers = $customerRepository->findAllWithingCompany($company);
        $table = new CustomersTable($customers, ['company' => $company]);
        return $this->render('customer/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {

        $customer = new Customer();
        $customer->setReferenceCompany($company);
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Customer created successfully');

            return $this->redirectToRoute('app_customer_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    #[IsGranted(CustomerVoterAttributes::CAN_VIEW_CUSTOMER, subject: 'customer')]
    public function show(Company $company, Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    #[IsGranted(CustomerVoterAttributes::CAN_EDIT_CUSTOMER, subject: 'customer')]
    public function edit(Request $request, Company $company, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Customer edited successfully');

            return $this->redirectToRoute('app_customer_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/edit.html.twig', [
            'customer' => $customer,
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    #[IsGranted(CustomerVoterAttributes::CAN_DELETE_CUSTOMER, subject: 'customer')]
    public function delete(Request $request, Customer $customer, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Customer deleted successfully');
        }

        return $this->redirectToRoute('app_customer_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
}
