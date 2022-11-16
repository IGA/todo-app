<?php

namespace App\Tests\Service\Task\Collector;

use App\Service\Task\Collector\DataCollector;
use App\Service\Task\Provider\BusinessTaskProvider;
use App\Service\Task\Provider\ITTaskProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class DataCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $itMockResponse = new MockResponse('[{"zorluk": 3, "sure": 6, "id": "IT Task 0"}]');
        $itMockHttpClient = new MockHttpClient($itMockResponse);
        $itProvider = new ITTaskProvider($itMockHttpClient);

        $businessMockResponse = new MockResponse('[{"Business Task 0": {"level": 1, "estimated_duration": 10}}]');
        $businessMockHttpClient = new MockHttpClient($businessMockResponse);
        $businessProvider = new BusinessTaskProvider($businessMockHttpClient);

        $taskProviders = new \ArrayIterator([$itProvider, $businessProvider]);
        $dataCollector = new DataCollector($taskProviders);

        $this->assertCount(2, $dataCollector->collect());
    }
}
