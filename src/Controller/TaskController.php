<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\TaskListRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TaskList;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Entity\Note;

class TaskController extends AbstractFOSRestController
{
    private $taskRepository;
    private $entityManager;

    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    public function getTaskActions(Task $task)
    {
        return $this->view($task, Response::HTTP_OK);
    }

    public function getTaskNotesAction(Task $task)
    {

        if ($task) {
            return $this->view($task->getNotes, Response::HTTP_OK);
        }

        return $this->view(['message' => 'somthing went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    /**
     * @return \FOS\RestBundle\View\View
     */
    public function deleteTaskAction(Task $task)
    {

        if ($task) {

            $this->entityManager->remove($task);
            $this->entityManager->flush();

            return $this->view($task, Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'somthing went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function statusTaskAction(Task $task)
    {

        if ($task) {

            $task->setIsComplete(!$task->getIsComplete());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task->getIsComplete(), Response::HTTP_NO_CONTENT);
        }

        return $this->view(['message' => 'somthing went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @Rest\RequestParam(name="note", description="note for the task", nullable=false)
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function postTaskNoteAction(ParamFetcher $paramFetcher, Task $task)
    {
        $noteString = $paramFetcher->get('note');
        if ($noteString) {

            if ($task) {
                $note = new Note();

                $note->setNote($noteString);
                $note->setTask($task);

                $task->addNote($note);

                $this->entityManager->persist($note);
                $this->entityManager->flush();

                return $this->view($note, Response::HTTP_OK);
            }
        }

        return $this->view(['message' => 'something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
