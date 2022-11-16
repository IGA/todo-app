<?php declare(strict_types=1);

namespace App\Service\Task\Collector;

use App\Service\Task\Provider\TaskProviderInterface;

class DataCollector
{
    /** @var iterable|TaskProviderInterface[] */
    private iterable $taskProviders;

    public function __construct(iterable $taskProviders)
    {
        $this->taskProviders = $taskProviders;
    }

    public function collect(): array
    {

        $tasks = [];
        foreach ($this->taskProviders as $taskProvider) {
            foreach ($taskProvider->getAll() as $task) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }
}