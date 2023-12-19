<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Company;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Table\CategoriesTable;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



#[Route('/company/{company}/category')]
class CategoryController extends AbstractController
{

    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    //Voter a mettre en place
    public function index(Company $company, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $table = new CategoriesTable($categories, ['company'=>$company]);
        return $this->render('category/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    //Voter a mettre en place
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            foreach($category->getProducts() as $product){
                $product->addCategory($category);
                $entityManager->persist($product);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_category_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    //Voter a mettre en place
    public function edit(Request $request, Company $company, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            foreach($category->getProducts() as $product){
                $product->addCategory($category);
                $entityManager->persist($product);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_category_index', [
                'company' => $company->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    //Voter a mettre en place
    public function delete(Request $request, Company $company, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {

            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

}