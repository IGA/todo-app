<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Week
{
    private Collection $assignments;
    private int $remainingPoints;
    private int $index;
    private bool $filled;

    public function __construct()
    {
        $this->assignments = new ArrayCollection();
        $this->index = 0;
        $this->filled = false;
    }

    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): Week
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments[] = $assignment;
        }

        return $this;
    }

    public function getRemainingPoints(): int
    {
        return $this->remainingPoints;
    }

    public function setRemainingPoints(int $remainingPoints): Week
    {
        $this->remainingPoints = $remainingPoints;
        return $this;
    }

    public function decreaseRemainingWorkHours(int $hours): Week
    {
        $this->remainingPoints -= $hours;
        return $this;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index): Week
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->filled;
    }

    /**
     * @param bool $filled
     * @return Week
     */
    public function setFilled(bool $filled): Week
    {
        $this->filled = $filled;
        return $this;
    }

}