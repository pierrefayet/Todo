<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{

    private function checkUpPermission(Task $task): bool
    {
        $currentUser = $this->getUser();
        if(!$currentUser instanceof User) {
            throw new \Exception('$currentUser n\'est pas une instance de User');
        }
        return $task->getUser() === $currentUser || in_array('ROLE_ADMIN', $currentUser->getRoles());
    }

    #[Route("/", name: "task_list")]
    public function listAction(TaskRepository $taskRepository): Response
    {
        $currentUser = $this->getUser();
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
        }

        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['user' => $currentUser])]);
    }

    #[Route("/tasks/create", name: "task_create")]
    public function createAction(Request $request, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/tasks/{id}/edit", name: "task_edit")]
    public function editAction(Task $task, Request $request, EntityManagerInterface $entityManager): RedirectResponse|Response
    {
        if (!$this->checkUpPermission($task)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette tâche.');

            return $this->redirectToRoute('task_list');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route("/tasks/{id}/toggle", name: "task_toggle")]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->checkUpPermission($task)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette tâche.');

            return $this->redirectToRoute('task_list');
        }

        $task->toggle(!$task->isDone());
        $entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route("/tasks/{id}/delete", name: "task_delete")]
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        if (!$this->checkUpPermission($task)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette tâche.');

            return $this->redirectToRoute('task_list');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
