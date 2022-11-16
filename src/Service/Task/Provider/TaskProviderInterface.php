<?php declare(strict_types=1);

namespace App\Service\Task\Provider;

interface TaskProviderInterface
{
    public function getAll(): array;
    public function validate(array $item): bool;
}