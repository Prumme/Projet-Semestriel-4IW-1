<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Data\TemplatesList;
use App\Form\RegisterCompanyAddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegisterCompanyType;
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
        $companyAddress = new BillingAddress($entityManager);
        $company = new Company();
        $formCompanyAddress = $this->createForm(RegisterCompanyAddressType::class,$companyAddress);
        $formCompany = $this->createForm(RegisterCompanyType::class,$company);
        $formCompanyAddress->handleRequest($request);
        $formCompany->handleRequest($request);

        if($formCompany->isSubmitted() && $formCompany->isValid() && $formCompanyAddress->isSubmitted() && $formCompanyAddress->isValid()){
            $company->setAddress($companyAddress);
            $entityManager->persist($companyAddress);
            $entityManager->persist($company);
            $entityManager->flush();

            $_SESSION['company_id'] = $company->getId();

            return $this->redirectToRoute('app_register_user');
        }

        return $this->render('security/register.html.twig', ['formCompany' => $formCompany, 'formCompanyAddress' => $formCompanyAddress]);
    }

    #[Route(path: '/register/user', name: 'app_register_user')]
    public function registerCompany(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $company = $entityManager->getRepository(Company::class)->find($_SESSION['company_id']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPassword()));
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

        return $this->render('security/register_user.html.twig', ['form' => $form]);
    }




}
