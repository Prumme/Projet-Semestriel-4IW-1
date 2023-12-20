<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserActivationType;
use App\Repository\UserRepository;
use App\Security\AuthentificableRoles;
use App\Security\Voter\Attributes\CompanyVoterAttributes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/{id}/activate', name: 'app_user_creation', methods: ['GET', 'POST'])]
    public function finish(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(UserActivationType::class, $user, [
            'action' => $this->generateUrl('app_user_creation', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $encodedPassword = $this->passwordEncoder->hashPassword($user, $data->getPassword());

            $user->setPassword($encodedPassword);

            $entityManager->persist($user);

            $entityManager->flush();
        }

        return $this->render('user/activate.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
