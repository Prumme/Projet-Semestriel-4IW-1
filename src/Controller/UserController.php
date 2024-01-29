<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserActivationType;
use App\Form\ForgetPasswordType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;
use App\Helper\URL;
use App\Service\EmailService;
use App\Data\TemplatesList;


#[Route('/user')]
class UserController extends AbstractController
{
    private $sendinblueService;
    private $urlHelper;

    private $passwordEncoder;

    public function __construct(EmailService $sendinblueService, URL $urlHelper, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->sendinblueService = $sendinblueService;
        $this->urlHelper = $urlHelper;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/{id}/activate', name: 'app_user_creation', methods: ['GET', 'POST'])]
    public function finish(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        if($user->isActivate()){
            throw $this->createnotfoundexception();
        }

        $form = $this->createForm(UserActivationType::class, $user, [
            'action' => $this->generateUrl('app_user_creation', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $encodedPassword = $this->passwordEncoder->hashPassword($user, $data->getPassword());

            $user->setPassword($encodedPassword);

            $user->setActivate(true);

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'Account Activate successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/activate.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/activate_owner', name: 'app_owner_creation', methods: ['GET', 'POST'])]
    public function finish_owner(User $user, EntityManagerInterface $entityManager): Response
    {

        if(!in_array('ROLE_COMPANY_ADMIN',$user->getRoles())){
            throw $this->createnotfoundexception();
        }

        if($user->isActivate()){
            throw $this->createnotfoundexception();
        }

        $user->setActivate(true);

        $entityManager->persist($user);

        $entityManager->flush();

        $this->addFlash('success', 'Account Activate successfully');

        return $this->redirectToRoute('app_login');

    }

    #[Route('/forget_password', name: 'forget_password', methods: ['GET'])]
    public function forget_password(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {

        $form = $this->createForm(ForgetPasswordType::class, null, [
            'action' => $this->generateUrl('forget_password'),
            'method' => 'GET',
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data->getEmail();

            $user = $entityManagerInterface->getRepository(User::class)->findOneBy(['email' => $email]);

            if($user){

                $to = $user->getEmail();
                $templateId = TemplatesList::FORGET_PASSWORD;
    
                $url = $this->urlHelper->generateUrl('/user/'.$user->getId().'/reset-password');
    
                $templateVariables = [
                    'name' => $user->getFirstName(),
                    'link' => $url
                ];
    
    
                $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

                $user->setResetPassword(true);
                $entityManagerInterface->persist($user);
                $entityManagerInterface->flush();

                return $this->redirectToRoute('message_sent', [
                    'email' => $email,
                    'message' => 'Email for reseting Password sent'
                ]);
                
            }
            $form->get('email')->addError(new FormError('Email not found'));

        }
        return $this->render('user/forget_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/reset-password', name: 'reset_password', methods: ['GET', 'POST'])]
    public function reset_password(Request $request,User $user, EntityManagerInterface $entityManager): Response
    {

        if(!$user->isResetPassword()){
            throw $this->createnotfoundexception();
        }

        $form = $this->createForm(ResetPasswordType::class, null, [
            'action' => $this->generateUrl('reset_password', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $encodedPassword = $this->passwordEncoder->hashPassword($user, $data->getPassword());

            $user->setPassword($encodedPassword);

            $user->setResetPassword(false);

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'Password Reset successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/reset_password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
