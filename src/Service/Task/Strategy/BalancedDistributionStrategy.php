<?php declare(strict_types=1);

namespace App\Service\Task\Strategy;

use App\Entity\Developer;
use App\Entity\Task;
use App\Model\AssignedTask;
use App\Model\Assignment;
use App\Model\Week;
use App\Repository\DeveloperRepository;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;

class BalancedDistributionStrategy implements StrategyInterface
{
    private TaskRepository $taskRepository;
    private DeveloperRepository $developerRepository;

    public function __construct(
        TaskRepository $taskRepository,
        DeveloperRepository $developerRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->developerRepository = $developerRepository;
    }

    public function assign(): array
    {
        $tasks = $this->taskRepository->findAll();
        $developers = $this->developerRepository->findAll();

        // İşleri önemsizden önemliye doğru sıralıyoruz
        usort($tasks, static fn (Task $a, Task $b) => ($a->getLevel() * $a->getDuration()) <=> ($b->getLevel() * $b->getDuration()));

        $totalWeeklyWorkforce = array_reduce($developers, static fn (?int $carry, Developer $developer) => $carry + ($developer->getPower() * $developer->getWorkingHours()));
        $totalWorkDurations = array_reduce($tasks, static fn (?int $carry, Task $task) => $carry + ($task->getDuration() * $task->getLevel()));
        $totalWeekCount = (int) ceil($totalWorkDurations / $totalWeeklyWorkforce);

        /** @var Week[] $weeks */
        $weeks = $this->prepareWeeks($developers, $totalWeekCount);

        foreach ($weeks as $week) {

            if (0 === count($tasks)) {
                break;
            }

            while (0 < count($tasks)) {

                if ($week->isFilled() || $week->getRemainingPoints() < 1) {
                    break;
                }

                // Dizideki ilk işi alıyoruz ve diziden siliyoruz
                $task = array_shift($tasks);
                $taskPoint = $task->getLevel() * $task->getDuration();

                // Seçilen hafta için uygun developer seçiyoruz
                $assignment = $this->choiceAssignment($week);

                if (null === $assignment) {
                    break;
                }

                // Eğer developerın bu hafta kalan iş gücü işe yetiyorsa
                if ($assignment->getRemainingPoints() > $taskPoint) {

                    // Aktif olan haftaya işle
                    $this->handleAssignment($week, $assignment, $task, $taskPoint);
                }
                // Eğer developerın bu hafta kalan iş gücü işe yetmiyorsa
                else {

                    // Önümüzdeki haftaya sarkacak olan puan
                    $remainingPointForTask = $taskPoint - $assignment->getRemainingPoints();

                    // Aktif olan haftaya işle
                    $this->handleAssignment($week, $assignment, $task, $assignment->getRemainingPoints());

                    // Kalan kısmı önümüzdeki haftaya işle
                    $nextWeek = $weeks[$week->getIndex()+1];
                    $this->handleAssignmentforNextWeek($nextWeek, $assignment, $task, $remainingPointForTask);
                }
            }
        }

        return $weeks;
    }

    private function choiceAssignment(Week $week): ?Assignment
    {
        /** @var Assignment[] $assignments */
        $assignmentsIterator = $week->getAssignments()->getIterator();

        // Developerları kalan iş gücüne göre büyükten küçüğe sıralıyoruz
        $assignmentsIterator->uasort(static fn (Assignment $a, Assignment $b) =>
            [$a->getTasks()->count(), $b->getRemainingPoints()]
            <=>
            [$b->getTasks()->count(), $a->getRemainingPoints()]);
        $assignments = new ArrayCollection(iterator_to_array($assignmentsIterator, false));

        foreach ($assignments as $assignmentItem) {
            if ($assignmentItem->getRemainingPoints() < 1) {
                $assignments->removeElement($assignmentItem);
            }
        }

        return $assignments->first() ?: null;
    }

    private function prepareWeeks(array $developers, int $totalWeekCount): array
    {
        $weeks = [];
        for ($i=0; $i<$totalWeekCount; $i++) {
            $week = new Week();
            $week->setIndex($i);

            $weekPoints = 0;
            foreach ($developers as $developer) {
                $assignment = new Assignment();
                $assignment->setDeveloper($developer);
                $assignment->setRemainingPoints($developer->getWorkingHours() * $developer->getPower());
                $week->addAssignment($assignment);
                $weekPoints += $assignment->getRemainingPoints();
            }

            $week->setRemainingPoints($weekPoints);
            $weeks[] = $week;
        }
        return $weeks;
    }

    private function handleAssignment(Week $week, Assignment $assignment, Task $task, int $point): void
    {
        // İş gücünü developerın iş gücünden düşürüyoruz
        $assignment->decreaseRemainingWorkHours($point);

        $assignedTask = new AssignedTask();
        $assignedTask->setTask($task);
        $assignedTask->setPoint($point);

        $assignment->addTask($assignedTask);

        if ($week->getRemainingPoints() > $point) {
            $week->decreaseRemainingWorkHours($point);
        } else {
            $week->decreaseRemainingWorkHours($week->getRemainingPoints());
            $week->setFilled(true);
        }
    }

    private function handleAssignmentforNextWeek(Week $nextWeek, Assignment $assignment, Task $task, int $point): void
    {
        // Önümüzdeki hafta için aynı developer'ı buluyoruz
        /** @var null|Assignment $nextWeekAssignment */
        $nextWeekAssignment = null;
        foreach ($nextWeek->getAssignments() as $assignmentItem) {
            if ($assignmentItem->getDeveloper() === $assignment->getDeveloper()) {
                $nextWeekAssignment = $assignmentItem;
                break;
            }
        }

        // İşi developer'a atıyoruz
        $assignedTask2 = new AssignedTask();
        $assignedTask2->setTask($task);
        $assignedTask2->setPoint($point);
        $nextWeekAssignment->addTask($assignedTask2);

        // Gelecek hafta için developer'ın kalan iş gücünü düşürüyoruz
        $nextWeekAssignment->decreaseRemainingWorkHours($point);

        if ($nextWeek->getRemainingPoints() > $point) {
            $nextWeek->decreaseRemainingWorkHours($point);
        } else {
            $nextWeek->decreaseRemainingWorkHours($nextWeek->getRemainingPoints());
            $nextWeek->setFilled(true);
        }
    }
}