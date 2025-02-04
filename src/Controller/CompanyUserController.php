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
use App\Service\EmailService;
use App\Data\TemplatesList;
use App\Helper\URL;

#[Route('/company/{company}/user')]
class CompanyUserController extends AbstractController
{
    private $sendinblueService;
    private $urlHelper;

    public function __construct(EmailService $sendinblueService, URL $urlHelper)
    {
        $this->sendinblueService = $sendinblueService;
        $this->urlHelper = $urlHelper;
    }

    #[Route('/', name: 'app_company_user_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_EDIT_COMPANY, subject: 'company')]
    public function index(Company $company, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAllWithinCompany($company);
        $table = new UserCompanyTable($users, ["company" => $company,'connectedUser'=>$this->getUser()]);
        return $this->render('company_user/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/delete', name: 'app_company_user_mass_delete', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_EDIT_COMPANY, subject: 'company')]
    public function massDelete(Request $request, Company $company, EntityManagerInterface $entityManager)
    {
        $selectedStr = $request->query->get('selected');
        if (!$selectedStr) return $this->redirectToRoute('app_company_user_index', ['company' => $company->getId()]);

        $selectedIds = explode(',', $selectedStr);
        $CSRFToken = $request->query->get('_token');
        if ($this->isCsrfTokenValid('mass-action-token', $CSRFToken)) {
            foreach ($selectedIds as $userId) {
                $user = $entityManager->getRepository(User::class)->find($userId);
                if ($this->isGranted(UserVoterAttributes::CAN_DELETE_USER, $user)){
                    $quotes = $user->getQuotes();
                    $canDelete = true;
                    foreach($quotes as $quote){
                        $newOwner = $company->getFirstUser();
                        if($newOwner !== $quote->getOwner()){
                            $quote->setOwner($company->getFirstUser());
                        }else {
                            $canDelete = false;
                            $this->addFlash('error', 'This user is the owner of some quotes.');
                            break;
                        }
                    }
                    if($canDelete){
                        $entityManager->remove($user);
                        $this->addFlash('success', 'User with email: ' . $user->getEmail() . ' deleted successfully');
                    }
                }
                else $this->addFlash('error', 'You are not allowed to delete user with email: ' . $user->getEmail());
            }
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_company_user_index', ['company' => $company->getId()]);
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
            // EMAIL SENDING
            $to = $user->getEmail();
            $templateId = TemplatesList::WELCOME_EMAIL;

            $url = $this->urlHelper->generateUrl('/user/'.$user->getId().'/activate');

            $templateVariables = [
                'name' => $user->getFirstName(),
                'company_name' => $user->getCompany()->getName(),
                'user_id' => $user->getId(),
                'link' => $url
            ];

            $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

            $this->addFlash('success', 'User Created Successfully');
            $this->addFlash('info', 'Email sent to user for activation');

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

    #[Route('/{id}/edit', name: 'app_company_user_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserVoterAttributes::CAN_EDIT_USER, subject: 'user')]
    public function edit(Request $request, Company $company, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'User Edited Successfully');

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
    public function delete(Request $request, Company $company, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $quotes = $user->getQuotes();

            $canDelete = true;
            foreach($quotes as $quote){
                $newOwner = $company->getFirstUser();
                if($newOwner !== $quote->getOwner()){
                    $quote->setOwner($company->getFirstUser());
                }else {
                    $canDelete = false;
                    $this->addFlash('error', 'This user is the owner of some quotes.');
                    break;
                }
            }

            if($canDelete){
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'User Deleted Successfully');
            }

        }

        return $this->redirectToRoute('app_company_user_index', [
            'company' => $company->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/activate', name: 'app_company_user_send_activate', methods: ['GET'])]
    #[IsGranted(UserVoterAttributes::CAN_EDIT_USER, subject: 'user')]
    public function sendActivate(Request $request, Company $company, User $user, EntityManagerInterface $entityManager): Response
    {

        if($user->isActivate() == true){
            $this->addFlash('error', 'User is already activated');
            return $this->redirectToRoute('app_company_user_index', [
                'company' => $company->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        $to = $user->getEmail();
        $templateId = TemplatesList::WELCOME_EMAIL;

        $url = $this->urlHelper->generateUrl('/user/'.$user->getId().'/activate');

        $templateVariables = [
            'name' => $user->getFirstName(),
            'company_name' => $user->getCompany()->getName(),
            'user_id' => $user->getId(),
            'link' => $url
        ];

        $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

        $this->addFlash('success', 'Activation email has been sent to user');

        return $this->redirectToRoute('app_company_user_index', [
            'company' => $company->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}
