<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use App\Security\Voter\Attributes\UserVoterAttributes;
use App\Table\UserCompanyTable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/{company}/user')]
class CompanyUserController extends AbstractController
{
    #[Route('/', name: 'app_company_user_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(Company $company, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllWithingCompany($company);
        $table = new UserCompanyTable($users,["company" => $company]);
        return $this->render('company_user/index.html.twig', [
            'table'=> $table->createTable(),
        ]);
    }

    #[Route('/delete', name: 'app_company_user_mass_delete', methods: ['GET'])]
    public function massDelete(Request $request,Company $company, EntityManagerInterface $entityManager){
        $selectedStr = $request->query->get('selected');
        if(!$selectedStr) return $this->redirectToRoute('app_company_user_index', ['company' => $company->getId()]);

        $selectedIds = explode(',',$selectedStr);
        $CSRFToken = $request->query->get('token');
        if ($this->isCsrfTokenValid('delete-item', $CSRFToken)){
            foreach ($selectedIds as $userId) {
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($this->isGranted(UserVoterAttributes::CAN_DELETE_USER, $user)) $entityManager->remove($user);
                else $this->addFlash('error', 'You are not allowed to delete user with email: '.$user->getEmail());
            }
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_company_user_index',['company' => $company->getId()]);
    }

    #[Route('/new', name: 'app_company_user_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_EDIT_COMPANY, subject: 'company')]
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $user->setCompany($company);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_company_user_index', [
                'company' => $company->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_user/new.html.twig', [
            'user' => $user,
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_user_show', methods: ['GET'])]
    #[IsGranted(UserVoterAttributes::CAN_VIEW_USER, subject: 'user')]
    public function show(Company $company,User $user): Response
    {
        return $this->render('company_user/show.html.twig', [
            'user' => $user,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoterAttributes::CAN_EDIT_USER, subject: 'user')]
    public function edit(Request $request,Company $company, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_company_user_index', [
                'company' => $company->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_user/edit.html.twig', [
            'user' => $user,
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_company_user_delete', methods: ['POST'])]
    #[IsGranted(UserVoterAttributes::CAN_DELETE_USER, subject: 'user')]
    public function delete(Request $request,Company $company, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_company_user_index', [
            'company' => $company->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}
