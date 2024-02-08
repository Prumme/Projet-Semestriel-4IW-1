<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use App\Table\CompanyTable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    #[IsGranted(AuthentificableRoles::ROLE_SUPER_ADMIN)]
    public function index_admin(CompanyRepository $companyRepository): Response
    {
        $companies = $companyRepository->findAll();
        $table = new CompanyTable($companies);
        return $this->render('company/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }


    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    #[IsGranted(AuthentificableRoles::ROLE_SUPER_ADMIN)]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($company);
            $entityManager->flush();

            $this->addFlash('success', 'Company created successfully');
            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_EDIT_COMPANY, subject: 'company')]
    public function edit(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Company updated successfully');
            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_delete', methods: ['POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_DELETE_COMPANY, subject: 'company')]
    public function delete(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager->remove($company);
            $entityManager->flush();
            
            $this->addFlash('success', 'Company deleted successfully');
        }

        return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
    }
}
