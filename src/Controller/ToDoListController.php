<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ToDoListController extends AbstractController
{
    #[Route('/', name: 'app_to_do_list')]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findAll();

        return $this->render('index.html.twig', [
            'tasks' => $tasks ?? []
        ]);
    }

    #[Route('/create', name: 'app_create_task', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $title = trim($request->request->get('title'));
        if (empty($title))
        return $this->redirectToRoute('app_to_do_list');

        $task = new Task;
        $task->setTitle($title);
        $em->persist($task);
        $em->flush();
        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/switch-status/{id}', name: 'app_switch_task_status')]
    public function switchStatus(int $id, EntityManagerInterface $em): Response
    {
        $task = $em->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->isStatus());
        $em->persist($task);
        $em->flush();
        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/delete/{id}', name: 'app_delete_task')]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $task = $em->getRepository(Task::class)->find($id);
        $em->remove($task);
        $em->flush();
        return $this->redirectToRoute('app_to_do_list');
    }
}
