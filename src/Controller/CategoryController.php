<?php 

namespace App\Controller;

use App\Security\AuthentificableRoles;
use Symfony\Bundle\SecurityBundle\Security;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use App\Security\Voter\Attributes\CategoryVoterAttributes;


#[Route('/company/{company}/category')]
class CategoryController extends AbstractController
{

    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function index(Company $company, CategoryRepository $categoryRepository, Security $security): Response
    {
        $categories = $categoryRepository->findAllWithinCompany($company);
        $table = new CategoriesTable($categories, ['company'=>$company,'security'=>$security]);
        return $this->render('category/index.html.twig', [
            'table' => $table->createTable(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    #[IsGranted(CompanyVoterAttributes::CAN_VIEW_COMPANY, subject: 'company')]
    public function new(Request $request, Company $company, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, ['company'=>$company]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCompany($company);
            $entityManager->persist($category);
            foreach($category->getProducts() as $product){
                $product->addCategory($category);
                $entityManager->persist($product);
            }
            
            $this->addFlash('success', 'Category created successfully');

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
    #[IsGranted(CategoryVoterAttributes::CAN_EDIT_CATEGORY, subject: 'category')]
    public function edit(Request $request, Company $company, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['company'=>$company]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($category);
            foreach($category->getProducts() as $product){
                $product->addCategory($category);
                $entityManager->persist($product);
            }

            $this->addFlash('success', 'Category updated successfully');

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
    #[IsGranted(AuthentificableRoles::ROLE_USER)]
    public function delete(Request $request, Company $company, Category $category, EntityManagerInterface $entityManager): Response
    {
        if(!$this->isGranted(CategoryVoterAttributes::CAN_DELETE_CATEGORY, $category)){
            $this->addFlash('error', 'You are not allowed to delete this category');
        } else if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $this->addFlash('success', 'Category deleted successfully');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_index', [
            'company' => $company->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

}