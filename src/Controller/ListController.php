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

class ListController extends AbstractFOSRestController
{

    // /**
    //  * @Rest\Get("/update", name="get_update")
    //  */
    // public function update()
    // {
    //     return ['message ' => 'update'];
    // }
    private $taskListRepository;
    private $entityManager;
    private $taskRepository;

    public function __construct(TaskListRepository $taskListRepository, EntityManagerInterface $entityManager, TaskRepository $taskRepository)

    {
        $this->taskRepository = $taskRepository;
        $this->taskListRepository = $taskListRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @return \FOS\RestBundle\View\View
     */
    public function getListsAction()
    {
        $data = $this->taskListRepository->findAll();
        return $this->view($data,  Response::HTTP_OK);
    }

    public function getListAction(TaskList $list)
    {
        return $this->view($list, Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="title",description="Title of the list", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return \FOS\RestBundle\View\View
     */
    public function postListAction(ParamFetcher $paramFetcher)
    {
        $title = $paramFetcher->get('title');
        if ($title) {
            $list = new TaskList();
            $list->setTitle($title);
            $this->entityManager->persist($list);
            $this->entityManager->flush();
            return $this->view($list, Response::HTTP_OK);
        }
        return $this->view(['title' => 'this cannot be null'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\RequestParam(name="title", description="Title of the new task", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function postListTaskAction(ParamFetcher $paramFetcher, TaskList $list)
    {

        if ($list) {
            $title = $paramFetcher->get('title');

            $task = new Task();
            $task->setTitle($title);
            $task->setList($list);

            $list->addTask($task);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->view($task, Response::HTTP_OK);
        }

        return $this->view(['message' => 'somthing went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }




    public function getListsTasksAction(TaskList $list)
    {

        return $this->view($list, Response::HTTP_OK);
    }

    /**
     * @Rest\FileParam(name="image",description="The Background of the List", nullable=false, image=true)
     * @param ParamFetcher $paramFetcher
     * @param int $id
     * @return int
     */
    public function backgroundListsAction(Request $request, ParamFetcher $paramFetcher, TaskList $list)
    {
        //todo : remove old file if any
        $currentBackground = $list->getBackground();
        if (!is_null($currentBackground)) {
            $filesystem = new Filesystem();
            $filesystem->remove(
                $this->getUploadDir() . $currentBackground
            );
        }
        $file = ($paramFetcher->get('image'));

        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

            $file->move(
                $this->getUploadDir(),
                $filename
            );

            $list->setBackground($filename);
            $list->setBackgroundPath('/images/' . $filename);

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $data = $request->getUriForPath(
                $list->getBackgroundPath()
            );

            return $this->view($data, Response::HTTP_OK);
        }

        return $this->view(['message' => 'Something went wrong'], Response::HTTP_BAD_REQUEST);
    }

    public function deleteListAction(TaskList $list)
    {

        $this->entityManager->remove($list);
        $this->entityManager->flush();

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\RequestParam(name="title",description="The new title for the list", nullable=false)
     * @param int $id
     * @return \FOS\RestBundle\View\View
     */
    public function patchListTitleAction(ParamFetcher $paramFetcher, TaskList $list)
    {
        $errors = [];
        $title = $paramFetcher->get('title');
        if (trim($title) !== '') {
            if ($list) {
                $list->setTitle($title);

                $this->entityManager->persist($list);
                $this->entityManager->flush();

                return $this->view(null, Response::HTTP_NO_CONTENT);
            }
            $errors[] = [
                'title' => 'This title cannot be empty'
            ];
        }
        $errors[] = [
            'list' => 'List not found'
        ];
    }

    private function getUploadDir()
    {
        return $this->getParameter('upload_dir');
    }
}
