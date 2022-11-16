<?php declare(strict_types=1);

namespace App\Service\Task\Strategy;

interface StrategyInterface
{
    public function assign(): array;
}