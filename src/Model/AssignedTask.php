<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\Task;

class AssignedTask
{
    private Task $task;
    private int $point;

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): AssignedTask
    {
        $this->task = $task;
        return $this;
    }

    public function getPoint(): int
    {
        return $this->point;
    }

    public function setPoint(int $point): AssignedTask
    {
        $this->point = $point;
        return $this;
    }

}