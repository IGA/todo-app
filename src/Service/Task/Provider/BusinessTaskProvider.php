<?php declare(strict_types=1);

namespace App\Service\Task\Provider;

use App\Entity\Task;
use App\Service\Task\Exception\InvalidProviderUrlException;
use App\Service\Task\Exception\InvalidResponseException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BusinessTaskProvider extends AbstractTaskProvider implements TaskProviderInterface
{
    private const API_URL = 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAll(): array
    {
        $response = $this->client->request(
            'GET',
            self::API_URL
        );

        if ($response->getStatusCode() !== 200) {
            throw new InvalidProviderUrlException("Status code is {$response->getStatusCode()}");
        }

        $items = [];
        foreach ($response->toArray() as $taskItem) {

            if (false === $this->validate($taskItem)) {
                throw new InvalidResponseException('Unexpected Data!');
            }

            foreach ($taskItem as $taskName => $taskDetails) {
                $task = new Task();
                $task
                    ->setName($taskName)
                    ->setDuration($taskDetails['estimated_duration'])
                    ->setLevel($taskDetails['level']);

                $items[] = $task;
            }
        }

        return $items;
    }

    public function validate(array $item): bool
    {
        return
            count($item) === 1
            && array_key_exists('estimated_duration', array_values($item)[0])
            && array_key_exists('level', array_values($item)[0])
        ;
    }
}