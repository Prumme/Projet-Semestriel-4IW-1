<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ContactUsType;
use Symfony\Component\Routing\Annotation\Route;
use App\Data\TemplatesList;
use App\Service\EmailService;

class DefaultController extends AbstractController
{

    public function __construct(private EmailService $sendinblueService){}

    public function home(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/contact-us', name: 'app_contact_us')]
    public function contactUs(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(ContactUsType::class, null, [
            'action' => $this->generateUrl('app_contact_us'),
            'method' => 'GET',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $to = "aprudhomme1@myges.fr";
            $templateId = TemplatesList::CONTACT_US;

            $templateVariables = [
                'name' => $data["firstname"] . " " . $data["lastname"],
                'email' => $data["email"],
                'content' => $data["content"],
            ];


            $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);

            $this->addFlash('success', 'Your message has been sent successfully!');

            return $this->redirectToRoute('app_contact_us');
        }

        return $this->render('default/contact_us.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView(),
        ]);
    }
}
