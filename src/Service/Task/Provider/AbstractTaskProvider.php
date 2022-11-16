<?php declare(strict_types=1);

namespace App\Service\Task\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AbstractTaskProvider
{
    protected HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
}