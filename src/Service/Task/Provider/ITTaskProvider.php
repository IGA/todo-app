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

class ITTaskProvider extends AbstractTaskProvider implements TaskProviderInterface
{
    private const API_URL = 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa';

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
            if ($this->validate($taskItem)) {
                $task = new Task();
                $task
                    ->setName($taskItem['id'])
                    ->setDuration($taskItem['sure'])
                    ->setLevel($taskItem['zorluk']);

                $items[] = $task;
            } else {
                throw new InvalidResponseException('Unexpected data!');
            }
        }

        return $items;
    }

    public function validate(array $item): bool
    {
        return
            array_key_exists('id', $item)
            && array_key_exists('sure', $item)
            && array_key_exists('zorluk', $item);
    }
}