<?php

namespace App\Controller;

use App\Security\AuthentificableRoles;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function index(Request $request): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
}
