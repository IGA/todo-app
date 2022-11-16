<?php declare(strict_types=1);

namespace App\Tests\Service\Task\Provider;

use App\Entity\Task;
use App\Service\Task\Exception\InvalidResponseException;
use App\Service\Task\Provider\BusinessTaskProvider;
use App\Service\Task\Provider\ITTaskProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class BusinessTaskProviderTest extends TestCase
{

    public function testGetAll(): void
    {
        $mockResponse = new MockResponse('[{"Business Task 0": {"level": 1, "estimated_duration": 10}},{"Business Task 1": {"level": 2, "estimated_duration": 14}}]');
        $client = new MockHttpClient($mockResponse);

        $provider = new BusinessTaskProvider($client);

        $tasks = $provider->getAll();

        $task = new Task();
        $task
           ->setName('Business Task 0')
           ->setLevel(1)
           ->setDuration(10);

        $this->assertEquals($task, $tasks[0]);

        $task = new Task();
        $task
            ->setName('Business Task 1')
            ->setLevel(2)
            ->setDuration(14);

        $this->assertEquals($task, $tasks[1]);
    }

    public function testGetAll_InvalidResponse(): void
    {
        $this->expectException(InvalidResponseException::class);
        $mockResponse = new MockResponse('[{"Business Task 0": {"levelx": 1, "estimated_duration": 10}},{"Business Task 1": {"level": 2, "estimated_duration": 14}}]');
        $client = new MockHttpClient($mockResponse);

        $provider = new ITTaskProvider($client);

        $provider->getAll();
    }
}