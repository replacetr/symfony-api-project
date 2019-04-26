<?php

namespace App\Serializer;

use Symfony\Component\Routing\RouterInterface;
use App\Entity\Note;

class CircularReferenceHandler
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    public function __invoke($object)
    {
        switch ($object) {
            case $object instanceof TaskList:
                return $this->router->generate('get_list', ['list' => $object->getId()]);
            case $object instanceof Task:
                return $this->router->generate('get_task', ['task' => $object->getId()]);
            case $object instanceof Note:
                return $this->router->generate('get_note', ['note' => $object->getId()]);
        }
        return $object->getId();
    }
}
