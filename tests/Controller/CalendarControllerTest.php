<?php

namespace App\Tests\Controller;

use App\Tests\DataProvider\CalendarDataProvider;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

final class CalendarControllerTest extends AbstractControllerTestCase
{
    public static function createCalendarProvider(): Generator
    {
        yield from CalendarDataProvider::createCalendarProvider();
    }

    public static function updateCalendarProvider(): Generator
    {
        yield from CalendarDataProvider::updateCalendarProvider();
    }

    public static function authenticationProvider(): Generator
    {
        yield from CalendarDataProvider::authenticationProvider();
    }

    public static function httpMethodProvider(): Generator
    {
        yield from CalendarDataProvider::httpMethodProvider();
    }

    public static function contentTypeProvider(): Generator
    {
        yield from CalendarDataProvider::contentTypeProvider();
    }

    public static function securityProvider(): Generator
    {
        yield from CalendarDataProvider::securityProvider();
    }

    public static function ownershipProvider(): Generator
    {
        yield from CalendarDataProvider::ownershipProvider();
    }

    public static function notFoundProvider(): Generator
    {
        yield from CalendarDataProvider::notFoundProvider();
    }

    public static function paginationProvider(): Generator
    {
        yield from CalendarDataProvider::paginationProvider();
    }

    public static function updateOwnershipProvider(): Generator
    {
        yield from CalendarDataProvider::updateOwnershipProvider();
    }

    #[Group('calendar')]
    #[Group('index')]
    public function testIndexCalendar(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        for ($i = 0; $i < 5; $i++) {
            $this->makeCalendarCreateRequest(
                $client,
                $this->validCalendarData,
                $token
            );
        }

        $this->makeCalendarListRequest(
            $client,
            $token
        );

        $this->assertResponseIsSuccessful();
        
        $data = $this->getDataFromResponse($client);
        $this->assertCount(5, $data['calendars']);
    }

    #[Group('calendar')]
    #[Group('create')]
    #[DataProvider('createCalendarProvider')]
    public function testCreateCalendar(array $data, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        if (empty($data)) {
            // For empty data, we need to send an empty JSON object
            $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
            $client->setServerParameter('CONTENT_TYPE', 'application/json');
            $client->request('POST', '/api/calendars', [], [], [], '{}');
        } else {
            $this->makeCalendarCreateRequest($client, $data, $token);
        }

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 201) {
            $responseData = json_decode($client->getResponse()->getContent(), true)['data'];
            $this->assertArrayHasKey('calendar', $responseData, 'Calendar data should be present in the response');
            $this->assertEquals($data['title'], $responseData['calendar']['title']);
            $this->assertEquals($data['description'], $responseData['calendar']['description']);
            $this->assertEquals($data['position'], $responseData['calendar']['position']);
            $this->assertEquals($data['color'], $responseData['calendar']['color']);
        }
    }

    #[Group('calendar')]
    #[Group('show')]
    public function testShowCalendar(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest(
            $client,
            $this->validCalendarData,
            $token
        );
        $data = $this->getDataFromResponse($client);
        $calendarId = $data['calendar']['id'];

        $this->makeCalendarShowRequest($client, $calendarId, $token);

        $this->assertResponseIsSuccessful();

        $data = $this->getDataFromResponse($client);
        $this->assertEquals($this->validCalendarData['title'], $data['calendar']['title']);
        $this->assertEquals($this->validCalendarData['description'], $data['calendar']['description']);
        $this->assertEquals($this->validCalendarData['position'], $data['calendar']['position']);
        $this->assertEquals($this->validCalendarData['color'], $data['calendar']['color']);
        $this->assertEquals($this->validRegisterData['email'], $data['calendar']['owner']);
        $this->assertArrayHasKey('createdAt', $data['calendar']);
        $this->assertArrayHasKey('updatedAt', $data['calendar']);
        $this->assertNotEmpty($data['calendar']['createdAt']);
        $this->assertNotEmpty($data['calendar']['updatedAt']);
    }

    #[Group('calendar')]
    #[Group('show')]
    #[DataProvider('notFoundProvider')]
    public function testShowCalendarNotFound(int $calendarId, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarShowRequest($client, $calendarId, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('show')]
    #[DataProvider('ownershipProvider')]
    public function testShowCalendarOwnership(bool $isOwner, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        // Create first user and calendar
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token1 = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $this->validCalendarData, $token1);
        $data = $this->getDataFromResponse($client);
        $calendarId = $data['calendar']['id'];

        if ($isOwner) {
            $token = $token1;
        } else {
            // Create second user
            $user2Data = $this->validRegisterData;
            $user2Data['email'] = 'user2@test.com';
            $this->makeRegisterRequest($client, $user2Data);
            $token = $this->getTokenFromResponse($client);
        }

        $this->makeCalendarShowRequest($client, $calendarId, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('update')]
    #[DataProvider('updateCalendarProvider')]
    public function testUpdateCalendar(array $data, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest(
            $client,
            $this->validCalendarData,
            $token
        );
        $responseData = $this->getDataFromResponse($client);
        $calendarId = $responseData['calendar']['id'];

        if (empty($data)) {
            // For empty data, we need to send an empty JSON object
            $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
            $client->setServerParameter('CONTENT_TYPE', 'application/json');
            $client->request('PUT', '/api/calendars/' . $calendarId, [], [], [], '{}');
        } else {
            $this->makeCalendarUpdateRequest($client, $calendarId, $data, $token);
        }

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseData = json_decode($client->getResponse()->getContent(), true)['data'];
            $this->assertArrayHasKey('calendar', $responseData, 'Calendar data should be present in the response');
            $this->assertEquals($data['title'], $responseData['calendar']['title']);
            $this->assertEquals($data['description'], $responseData['calendar']['description']);
            $this->assertEquals($data['position'], $responseData['calendar']['position']);
            $this->assertEquals($data['color'], $responseData['calendar']['color']);
        }
    }

    #[Group('calendar')]
    #[Group('update')]
    #[DataProvider('notFoundProvider')]
    public function testUpdateCalendarNotFound(int $calendarId, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarUpdateRequest($client, $calendarId, $this->validCalendarData, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('update')]
    #[DataProvider('updateOwnershipProvider')]
    public function testUpdateCalendarOwnership(bool $isOwner, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        // Create first user and calendar
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token1 = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $this->validCalendarData, $token1);
        $data = $this->getDataFromResponse($client);
        $calendarId = $data['calendar']['id'];

        if ($isOwner) {
            $token = $token1;
        } else {
            // Create second user
            $user2Data = $this->validRegisterData;
            $user2Data['email'] = 'user2@test.com';
            $this->makeRegisterRequest($client, $user2Data);
            $token = $this->getTokenFromResponse($client);
        }

        $updateData = $this->validCalendarData;
        $updateData['title'] = 'Updated Title';

        $this->makeCalendarUpdateRequest($client, $calendarId, $updateData, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('delete')]
    public function testDeleteCalendar(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest(
            $client,
            $this->validCalendarData,
            $token
        );
        $data = $this->getDataFromResponse($client);
        $calendarId = $data['calendar']['id'];

        $this->makeCalendarDeleteRequest($client, $calendarId, $token);

        $this->assertResponseStatusCodeSame(204);
    }

    #[Group('calendar')]
    #[Group('delete')]
    #[DataProvider('notFoundProvider')]
    public function testDeleteCalendarNotFound(int $calendarId, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarDeleteRequest($client, $calendarId, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('delete')]
    #[DataProvider('updateOwnershipProvider')]
    public function testDeleteCalendarOwnership(bool $isOwner, int $expectedStatusCode): void
    {
        $client = $this->createClient();

        // Create first user and calendar
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token1 = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $this->validCalendarData, $token1);
        $data = $this->getDataFromResponse($client);
        $calendarId = $data['calendar']['id'];

        if ($isOwner) {
            $token = $token1;
            $expectedStatusCode = 204; // Owner can delete
        } else {
            // Create second user
            $user2Data = $this->validRegisterData;
            $user2Data['email'] = 'user2@test.com';
            $this->makeRegisterRequest($client, $user2Data);
            $token = $this->getTokenFromResponse($client);
            $expectedStatusCode = 403; // Non-owner cannot delete
        }

        $this->makeCalendarDeleteRequest($client, $calendarId, $token);
        $this->assertResponseStatusCodeSame($expectedStatusCode);
    }

    #[Group('calendar')]
    #[Group('authentication')]
    #[DataProvider('authenticationProvider')]
    public function testAuthentication(?string $token, int $expectedStatus): void
    {
        $client = $this->createClient();

        if ($token) {
            $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
        }

        $client->request('GET', '/api/calendars');

        self::assertResponseStatusCodeSame($expectedStatus);
    }

    #[Group('calendar')]
    #[Group('http-methods')]
    #[DataProvider('httpMethodProvider')]
    public function testWrongHttpMethods(string $method, string $endpoint, array $allowedStatusCodes): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
        $client->request($method, $endpoint);

        $statusCode = $client->getResponse()->getStatusCode();
        self::assertContains(
            $statusCode,
            $allowedStatusCodes,
            "Wrong method {$method} for {$endpoint} should return one of: " . implode(', ', $allowedStatusCodes)
        );
    }

    #[Group('calendar')]
    #[Group('content-type')]
    #[DataProvider('contentTypeProvider')]
    public function testContentType(string $method, string $endpoint, ?string $contentType, $expectedStatus): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        if ($contentType) {
            $headers['CONTENT_TYPE'] = $contentType;
        }

        $client->request($method, $endpoint, [], [], $headers, json_encode($this->validCalendarData));

        if (is_array($expectedStatus)) {
            self::assertContains($client->getResponse()->getStatusCode(), $expectedStatus);
        } else {
            self::assertResponseStatusCodeSame($expectedStatus);
        }
    }

    #[Group('calendar')]
    #[Group('security')]
    #[DataProvider('securityProvider')]
    public function testSecurity(array $calendarData): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $calendarData, $token);

        $statusCode = $client->getResponse()->getStatusCode();

        if ($statusCode === 201) {
            $responseData = $this->getJsonResponse($client);
            // Data should be stored as plain text, not executed
            self::assertEquals($calendarData['title'], $responseData['data']['calendar']['title']);
            self::assertEquals($calendarData['description'], $responseData['data']['calendar']['description']);
        } else {
            // Should fail validation due to invalid characters
            self::assertResponseStatusCodeSame(422);
        }
    }

    #[Group('calendar')]
    #[Group('pagination')]
    #[DataProvider('paginationProvider')]
    public function testPagination(?int $page, ?int $limit, int $expectedMaxCount): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        // Create test calendars
        for ($i = 0; $i < 15; $i++) {
            $calendarData = $this->validCalendarData;
            $calendarData['title'] = "Calendar {$i}";
            $this->makeCalendarCreateRequest($client, $calendarData, $token);
        }

        $params = [];
        if ($page !== null) {
            $params['page'] = $page;
        }
        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        $queryString = !empty($params) ? '?' . http_build_query($params) : '';
        $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
        $client->request('GET', '/api/calendars' . $queryString);

        self::assertResponseIsSuccessful();

        $responseData = $this->getJsonResponse($client);
        $actualCount = count($responseData['data']['calendars']);
        
        self::assertGreaterThan(0, $actualCount, 'Should return at least one calendar');
        self::assertLessThanOrEqual($expectedMaxCount, $actualCount, 'Should not return more calendars than allowed by pagination');
    }

    #[Group('calendar')]
    #[Group('performance')]
    public function testConcurrentCalendarOperations(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $startTime = microtime(true);

        for ($i = 1; $i <= 10; $i++) {
            $calendarData = $this->validCalendarData;
            $calendarData['title'] = "Calendar {$i}";

            $this->makeCalendarCreateRequest($client, $calendarData, $token);
            self::assertResponseIsSuccessful();
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        self::assertLessThan(5, $duration, "10 calendar creations took too long: {$duration} seconds");
    }

}
