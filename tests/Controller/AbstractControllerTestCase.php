<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\Attributes\Internal;

abstract class AbstractControllerTestCase extends WebTestCase
{

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Drop and recreate the database
        // Drop and recreate the database (без вывода)
        exec('php bin/console doctrine:schema:drop --env=test --force > /dev/null 2>&1');
        exec('php bin/console doctrine:schema:create --env=test > /dev/null 2>&1');
    }
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        $client = parent::createClient($options, $server);

        // Clean up the database after each test
        $client->getContainer()->get('doctrine')->getManager()->clear();
        $connection = $client->getContainer()->get('doctrine')->getManager()->getConnection();
        $platform = $connection->getDatabasePlatform();
        $quotedTable = $platform->quoteIdentifier('user');
        $connection->executeStatement('DELETE FROM ' . $quotedTable);

        return $client;
    }


    protected function makeRegisterRequest(KernelBrowser $client, array $data): KernelBrowser
    {
        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        return $client;
    }

    protected function makeLoginRequest(KernelBrowser $client, array $data): KernelBrowser
    {
        $client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        return $client;
    }

    protected function getTokenFromResponse(KernelBrowser $client): ?string
    {
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        return $content['data']['token'] ?? null;
    }

    protected function makeProfileUpdateRequest(KernelBrowser $client, array $data, ?string $token = null): KernelBrowser
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $client->request(
            'PUT',
            '/api/profile',
            [],
            [],
            $headers,
            json_encode($data)
        );

        return $client;
    }


    protected function getJsonResponse(KernelBrowser $client): array
    {
        return json_decode($client->getResponse()->getContent(), true);
    }

    protected function makeProfileRequest(KernelBrowser $client, ?string $token = null): KernelBrowser
    {
        $headers = [];
        if ($token) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer ' . $token;
        }

        $client->request(
            'GET',
            '/api/profile',
            [],
            [],
            $headers
        );

        return $client;
    }
}
