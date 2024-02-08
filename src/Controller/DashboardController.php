<?php

namespace App\Controller;

use App\Entity\BillingRow;
use App\Repository\BillingRowRepository;
use App\Security\AuthentificableRoles;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\BillingAddressRepository;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function index(BillingRowRepository $billingRowRepository): Response
    {
        $dataBestFiveProducts = $billingRowRepository->bestFiveProduct($this->getUser()->getCompany()->getId());

        $dataProductSelled = $billingRowRepository->productSelled($this->getUser()->getCompany()->getId());

        $dataTotalEarned = $billingRowRepository->totalEarned($this->getUser()->getCompany()->getId());


        return $this->render('dashboard/index.html.twig',
            [
                'dataBestFiveProducts' => json_encode($dataBestFiveProducts),
                'dataProductSelled' => json_encode($dataProductSelled),
                'dataTotalEarned' => $dataTotalEarned[0]['data']
            ]);
    }
}
