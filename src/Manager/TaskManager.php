<?php declare(strict_types=1);

namespace App\Manager;

use App\Service\Task\Collector\DataCollector;
use App\Service\Task\Strategy\StrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    private DataCollector $dataCollector;
    private EntityManagerInterface $entityManager;
    private StrategyInterface $strategy;

    public function __construct(
        DataCollector $dataCollector,
        EntityManagerInterface $entityManager,
        StrategyInterface $strategy
    )
    {
        $this->dataCollector = $dataCollector;
        $this->entityManager = $entityManager;
        $this->strategy = $strategy;
    }

    public function collect(): void
    {
        foreach ($this->dataCollector->collect() as $task) {
            $this->entityManager->persist($task);
        }

        $this->entityManager->flush();
    }

    public function assign(): array
    {
        return $this->strategy->assign();
    }

}