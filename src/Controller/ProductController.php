<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Product;
use App\Form\ProductType;
use App\Table\ProductsTable;

use App\Repository\ProductRepository;
use App\Security\AuthentificableRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use App\Security\Voter\Attributes\ProductVoterAttributes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/company/{company}/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(Company $company, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $table = new ProductsTable($products, ['company'=>$company]);
        return $this->render('product/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
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

            $this->addFlash('success', 'Product created successfully');

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
    #[IsGranted(ProductVoterAttributes::CAN_EDIT_PRODUCT, subject: 'product')]
    public function edit(Request $request, Company $company, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Product updated successfully');

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
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function delete(Request $request, Product $product, Company $company, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted(ProductVoterAttributes::CAN_DELETE_PRODUCT, $product)) {
            $this->addFlash('error', 'You cannot delete this product');
        } else if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully');
        } 

        return $this->redirectToRoute('app_product_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

}
