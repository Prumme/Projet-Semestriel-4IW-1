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
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Helper\URL;
use App\Service\EmailService;

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
            $error = $this->checkInputRegister($request->request, $entityManager);

            if($error != ""){
                return $this->render('security/register.html.twig', ['error' => $error, 'form' => $form]);
            }


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


    private function checkInputRegister($data, EntityManagerInterface $entityManager): String
    {

        if($data->get('password') != $data->get('password_verification')){
            return "Password and password verification are not the same";
        }

        if($data->get('name') == ""){
            return "Name is empty";
        }

        if($data->get('siret') == ""){
            return "Siret is empty";
        }

        if($data->get('vat_number') == ""){
            return "Vat number is empty";
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
}
