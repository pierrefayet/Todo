<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    #[Route("/users", name: "user_list")]
    public function listAction(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    #[Route("/users/create", name:"user_create")]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hashed): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hashed->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/users/{id}/edit", name:"user_edit")]
    public function editUser(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hashed): RedirectResponse|Response
    {
        $form = $this->createForm(UserType::class, $user);
        if (!$this->isGranted('ROLE_ADMIN')) {
            $form->remove('roles');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hashed->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('user_list');
            }

            return $this->redirectToRoute('task_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    #[Route("/users/{id}/delete", name: "user_delete")]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimée.');

        return $this->redirectToRoute('user_list');
    }
}
