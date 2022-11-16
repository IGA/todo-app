<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\Developer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Assignment
{
    private Developer $developer;
    private Collection $tasks;
    private int $remainingPoints;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getDeveloper(): Developer
    {
        return $this->developer;
    }

    public function setDeveloper(Developer $developer): Assignment
    {
        $this->developer = $developer;
        return $this;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(AssignedTask $task): Assignment
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
        }

        return $this;
    }

    public function getRemainingPoints(): int
    {
        return $this->remainingPoints;
    }

    public function setRemainingPoints(int $remainingPoints): Assignment
    {
        $this->remainingPoints = $remainingPoints;
        return $this;
    }

    public function decreaseRemainingWorkHours(int $hours): Assignment
    {
        $this->remainingPoints -= $hours;
        return $this;
    }

}