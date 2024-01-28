<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {

        $error = "";

        $form = $this->createForm(RegisterType::class);
        // dd($request);
        // dd($form);
        $form->handleRequest($request);


        if($form->isSubmitted()) {

            
            $company = new Company();

            $company->setName($request->request->get('name'));
            $company->setSiret($request->request->get('siret'));
            $company->setVatNumber($request->request->get('vat_number'));
                


            $entityManager->persist($company);
            $entityManager->flush();

            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstname($request->request->get('firstname'));
            $user->setLastname($request->request->get('lastname'));
            $user->setPassword($request->request->get('password'));
            $user->setRoles(["ROLE_USER", "ROLE_COMPANY_ADMIN"]);
            $user->setCompany($company);
               
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');

        }

        return $this->render('security/register.html.twig', ['error' => $error, 'form' => $form]);
    }

    #[Route(path: '/register/company', name: 'app_register_company')]
    public function registerCompany(): Response
    {
        
        return $this->render('security/register_company.html.twig');
    }
    
    #[Route(path: '/register/employee', name: 'app_register_employee')]
    public function registerEmployee(): Response
    {
        return $this->render('security/register_employee.html.twig');
    }
}
