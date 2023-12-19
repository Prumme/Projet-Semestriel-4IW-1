<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProductType;
use App\Table\ProductsTable;
use App\Security\AuthentificableRoles;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/company/{company}/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    //Voter a mettre en place
    public function index(Company $company, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $table = new ProductsTable($products, ['company'=>$company]);
        return $this->render('product/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    //Voter a mettre en place
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {

        $product = new Product();
        $product->setCompany($company);
        $product->setUserId($this->getUser());
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    //Voter a mettre en place
    public function edit(Request $request, Company $company, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            return $this->redirectToRoute('app_product_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    //Voter a mettre en place
    public function delete(Request $request, Product $product, Company $company, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

}
