<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private $sendinblueService;

    public function __construct(EmailService $sendinblueService)
    {
        $this->sendinblueService = $sendinblueService;
    }

    /**
     * @Route("/send-email", name="send_email")
     */
    #[Route('/mail', name: 'Mail_test')]
    public function sendEmail(): Response
    {
        // Préparez ici les données de l'e-mail, comme l'adresse du destinataire, le sujet, le contenu, etc.
        $to = 'aurelien23.p@gmail.com';
        $subject = 'Votre sujet d\'e-mail ici';
        $content = 'Le contenu de votre e-mail ici';

        // Utilisez le service Sendinblue pour envoyer l'e-mail
        $result = $this->sendinblueService->sendEmail($to, $subject, $content);

        if ($result) {
            // Gérer la réponse réussie
            return new Response('E-mail envoyé avec succès.');
        } else {
            // Gérer l'échec de l'envoi
            return new Response('Échec de l\'envoi de l\'e-mail.');
        }
    }
}
