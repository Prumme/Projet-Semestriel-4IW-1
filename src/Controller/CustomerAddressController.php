<?php

namespace App\Controller;

use App\Entity\BillingAddress;
use App\Entity\Company;
use App\Entity\Customer;
use App\Form\CustomerBillingAddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/company/{company}/customer/{customer}/address')]
class CustomerAddressController extends AbstractController
{
    #[Route('/new', name: 'app_customer_address_new',methods: ['GET', 'POST'])]
    public function new(Request $request, Company $company, Customer $customer, EntityManagerInterface $entityManager): Response
    {
        $address = new BillingAddress($entityManager);
        $address->setCustomer($customer);
        $form = $this->createForm(CustomerBillingAddressType::class, $address);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();
            $this->addFlash('success', 'Billing row created successfully');
            return $this->redirectToRoute('app_customer_edit', [
                'company' => $company->getId(),
                'id' => $customer->getId(),
            ]);
        }

        return $this->render('billingAddress/new.html.twig', [
            'form' => $form,
            'address' => $address,
            'customer' => $customer,
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_address_edit',methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, Customer $customer,BillingAddress $address, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerBillingAddressType::class, $address);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();
            $this->addFlash('success', 'Billing row edited successfully');
            return $this->redirectToRoute('app_customer_edit', [
                'company' => $company->getId(),
                'id' => $customer->getId(),
            ]);
        }

        return $this->render('billingAddress/edit.html.twig', [
            'form' => $form,
            'address' => $address,
            'customer' => $customer,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_customer_address_delete',methods: ['POST'])]
    public function delete(Request $request, Company $company, Customer $customer, BillingAddress $billingAddress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $billingAddress->getId(), $request->request->get('_token'))) {
            $entityManager->remove($billingAddress);
            $entityManager->flush();
            $this->addFlash('success', 'Billing row deleted successfully');
        }

        return $this->redirectToRoute('app_customer_edit', [
            'company' => $company->getId(),
            'id' => $customer->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
}
