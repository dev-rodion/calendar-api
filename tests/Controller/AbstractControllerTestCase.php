<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\Attributes\Internal;

abstract class AbstractControllerTestCase extends WebTestCase
{
    protected array $validRegisterData = [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'test@test.com',
        'password' => 'password123',
        'confirmPassword' => 'password123',
    ];

    protected array $validLoginData = [
        'email' => 'test@test.com',
        'password' => 'password123',
    ];

    protected array $validUpdateProfileData = [
        'firstName' => 'Updated',
        'lastName' => 'User',
        'email' => 'updated@test.com',
    ];

    protected array $validCalendarData = [
        'title' => 'Test Calendar',
        'description' => 'This is a test calendar',
        'position' => 0,
        'color' => null,
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Drop and recreate the database (without output)
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

    protected function makeAuthenticatedRequest(
        KernelBrowser $client,
        string $method,
        string $uri,
        string $token,
        array $data = [],
        array $extraHeaders = []
    ): void {
        $server = [
            'HTTP_Authorization' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
            ...$extraHeaders
        ];

        $content = !empty($data) ? json_encode($data) : null;

        $client->request($method, $uri, server: $server, content: $content);
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

    protected function getDataFromResponse(KernelBrowser $client): ?array
    {
        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        if (isset($content['data'])) {
            return $content['data'];
        }
        return null;
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

        $this->makeAuthenticatedRequest(
            $client,
            'PUT',
            '/api/profile',
            $token,
            $data,
            $headers
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

    protected function makeCalendarListRequest(KernelBrowser $client, ?string $token = null): KernelBrowser
    {
        $this->makeAuthenticatedRequest(
            $client,
            'GET',
            '/api/calendars',
            $token
        );

        return $client;
    }

    protected function makeCalendarCreateRequest(KernelBrowser $client, array $data, ?string $token = null): KernelBrowser
    {
        $this->makeAuthenticatedRequest(
            $client,
            'POST',
            '/api/calendars',
            $token,
            $data,
        );

        return $client;
    }

    protected function makeCalendarShowRequest(KernelBrowser $client, int $id, ?string $token = null): KernelBrowser
    {
        $this->makeAuthenticatedRequest(
            $client,
            'GET',
            '/api/calendars/' . $id,
            $token
        );

        return $client;
    }

    protected function makeCalendarUpdateRequest(KernelBrowser $client, int $id, array $data, ?string $token = null): KernelBrowser
    {
        $this->makeAuthenticatedRequest(
            $client,
            'PUT',
            '/api/calendars/' . $id,
            $token,
            $data,
        );

        return $client;
    }

    protected function makeCalendarDeleteRequest(KernelBrowser $client, int $id, ?string $token = null): KernelBrowser
    {
        $this->makeAuthenticatedRequest(
            $client,
            'DELETE',
            '/api/calendars/' . $id,
            $token
        );

        return $client;
    }
}