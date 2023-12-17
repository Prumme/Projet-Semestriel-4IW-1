<?php

namespace App\Controller;

use App\Security\AuthentificableRoles;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/home')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_home_index', methods: ['GET'])]
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

}
