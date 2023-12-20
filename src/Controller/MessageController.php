<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends AbstractController
{
    #[Route('/message_sent', name: 'message_sent')]
    public function message_sent(Request $request): Response
    {
        $message = $request->get('message');
        $email = $request->get('email');
        return $this->render('message/sent.html.twig',
            [
                'message' => $message,
                'email' => $email
            ]);
    }
}
