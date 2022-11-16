<?php declare(strict_types=1);

namespace App\Tests\Service\Task\Provider;

use App\Entity\Task;
use App\Service\Task\Exception\InvalidResponseException;
use App\Service\Task\Provider\ITTaskProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ITTaskProviderTest extends TestCase
{
    public function testGetAll(): void
    {
        $mockResponse = new MockResponse('[{"zorluk": 3, "sure": 6, "id": "IT Task 0"},{"zorluk": 2, "sure": 2, "id": "IT Task 1"}]');
        $client = new MockHttpClient($mockResponse);

        $provider = new ITTaskProvider($client);

        $tasks = $provider->getAll();

        $task = new Task();
        $task
           ->setName('IT Task 0')
           ->setLevel(3)
           ->setDuration(6);

        $this->assertEquals($task, $tasks[0]);

        $task = new Task();
        $task
            ->setName('IT Task 1')
            ->setLevel(2)
            ->setDuration(2);

        $this->assertEquals($task, $tasks[1]);
    }

    public function testGetAll_InvalidResponse(): void
    {
        $this->expectException(InvalidResponseException::class);
        $mockResponse = new MockResponse('[{"zorxluk": 3, "sure": 6, "id": "IT Task 0"},{"zorluk": 2, "sure": 2, "id": "IT Task 1"}]');
        $client = new MockHttpClient($mockResponse);

        $provider = new ITTaskProvider($client);

        $provider->getAll();

        $this->expectException(InvalidResponseException::class);
    }
}