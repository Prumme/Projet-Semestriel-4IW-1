<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/default', name: 'default_index')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/say-my-name/{name}', name: 'default_say_my_name')]
    public function sayMyName(String $name): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => $name,
        ]);
    }

    #[Route('/chips-pills', name: 'default_chips_pills')]
    public function chipsPills(): Response
    {
        return $this->render('default/chips-pills.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
