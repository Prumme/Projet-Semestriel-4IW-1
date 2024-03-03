<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Data\TemplatesList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Helper\URL;
use App\Service\EmailService;

use App\Entity\BillingAddress;
use App\Form\RegisterUserType;

class SecurityController extends AbstractController
{


    function __construct(private UserPasswordHasherInterface $passwordEncoder, private URL $urlHelper, private EmailService $sendinblueService){}

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
        $form->handleRequest($request);


        if($form->isSubmitted()) {
            $error = $this->checkInputRegisterCompany($request->request);
            
            if($error != ""){
                return $this->render('security/register.html.twig', ['error' => $error, 'form' => $form]);
            }


            $billingAddress = new BillingAddress($entityManager);

            $billingAddress->setCountryCode($request->request->get('country_code'));
            $billingAddress->setCity($request->request->get('city'));
            $billingAddress->setZipCode(intval($request->request->get('zip_code')));
            $billingAddress->setAddressLine1($request->request->get('address_line_1'));
            if($request->request->get('address_line_2') != ""){
                $billingAddress->setAddressLine2($request->request->get('address_line_2'));
            }


            $entityManager->persist($billingAddress);
            $entityManager->flush();

            $company = new Company();

            $company->setName($request->request->get('name'));
            $company->setSiret($request->request->get('siret'));
            $company->setVatNumber($request->request->get('vat_number'));
            $company->setAddress($billingAddress);
                


            $entityManager->persist($company);
            $entityManager->flush();

            $_SESSION['company_id'] = $company->getId();

            return $this->redirectToRoute('app_register_user');

            

        }

        return $this->render('security/register.html.twig', ['error' => $error, 'form' => $form]);
    }

    #[Route(path: '/register/user', name: 'app_register_user')]
    public function registerCompany(Request $request, EntityManagerInterface $entityManager): Response
    {
        $error = "";

        $form = $this->createForm(RegisterUserType::class);
        $form->handleRequest($request);


        if($form->isSubmitted()) {

            $error = $this->checkInputRegister($request->request, $entityManager);
           

            if($error != ""){
                
                return $this->render('security/register_user.html.twig', ['error' => $error, 'form' => $form]);
            }
        
            $company = $entityManager->getRepository(Company::class)->find($_SESSION['company_id']);

            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstname($request->request->get('firstname'));
            $user->setLastname($request->request->get('lastname'));
            $user->setPassword($this->passwordEncoder->hashPassword($user, $request->request->get('password')));
            $user->setRoles(["ROLE_USER", "ROLE_COMPANY_ADMIN"]);
            $user->setCompany($company);
                
            $entityManager->persist($user);
            $entityManager->flush();

            // EMAIL SENDING
            $to = $user->getEmail();
            $templateId = TemplatesList::WELCOME_EMAIL_OWNER;

            $url = $this->urlHelper->generateUrl('/user/'.$user->getId().'/activate_owner');

            $templateVariables = [
                'name' => $user->getFirstName(),
                'company_name' => $user->getCompany()->getName(),
                'link' => $url
            ];

            $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

            return $this->redirectToRoute('message_sent', [
                'email' => $to,
                'message' => 'Email for verifying your address sent'
            ]);

        }

        return $this->render('security/register_user.html.twig', ['error' => $error, 'form' => $form]);
    }
    
    #[Route(path: '/register/employee', name: 'app_register_employee')]
    public function registerEmployee(): Response
    {
        return $this->render('security/register_employee.html.twig');
    }


    private function checkInputRegister($data, EntityManagerInterface $entityManager): String
    {

        if($data->get('password') != $data->get('password_verification')){
            return "Password and password verification are not the same";
        }

        if($data->get('email') == ""){
            return "Email is empty";
        }

        if($data->get('firstname') == ""){
            return "Firstname is empty";
        }

        if($data->get('lastname') == ""){
            return "Lastname is empty";
        }

        // check email does not aloready exist
        if($entityManager->getRepository(User::class)->findOneBy(['email' => $data->get('email')]) != null){
            return "Email already exist";
        }

        return "";
    }

    private function checkInputRegisterCompany($data): String
    {

        if($data->get('name') == ""){
            return "Name is empty";
        }

        if($data->get('siret') == ""){
            return "Siret is empty";
        }

        if($data->get('vat_number') == ""){
            return "Vat number is empty";
        }

        if($data->get('country_code') == ""){
            return "Country code is empty";
        }

        if($data->get('city') == ""){
            return "City is empty";
        }

        if($data->get('zip_code') == ""){
            return "Zip code is empty";
        }

        if($data->get('address_line_1') == ""){
            return "Address line 1 is empty";
        }

        return "";
    }
}
