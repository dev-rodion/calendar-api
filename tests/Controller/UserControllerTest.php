<?php

namespace App\Tests\Controller;

use App\Tests\DataProvider\UserDataProvider;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

final class UserControllerTest extends AbstractControllerTestCase
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


    public static function registrationProvider(): Generator
    {
        yield from UserDataProvider::registrationProvider();
    }

    public static function loginProvider(): Generator
    {
        yield from UserDataProvider::loginProvider();
    }

    public static function updateProfileProvider(): Generator
    {
        yield from UserDataProvider::updateProfileProvider();
    }

    public static function authenticationProvider(): Generator
    {
        yield from UserDataProvider::authenticationProvider();
    }

    public static function httpMethodProvider(): Generator
    {
        yield from UserDataProvider::httpMethodProvider();
    }

    public static function contentTypeProvider(): Generator
    {
        yield from UserDataProvider::contentTypeProvider();
    }

    public static function securityProvider(): Generator
    {
        yield from UserDataProvider::securityProvider();
    }


    #[Group('user')]
    #[Group('register')]
    #[DataProvider('registrationProvider')]
    public function testRegister(array $data, int $expectedStatusCode): void
    {
        $client = static::createClient();
        $this->makeRegisterRequest($client, $data);
        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 201) {
            $responseData = json_decode($client->getResponse()->getContent(), true)['data'];
            $this->assertArrayHasKey('token', $responseData, 'Token should be present in the response');
            $this->assertArrayHasKey('user', $responseData, 'User data should be present in the response');
        }
    }



    #[Group('user')]
    #[Group('register')]
    public function testRegisterWithDuplicateEmail(): void
    {
        $client = static::createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $this->assertResponseStatusCodeSame(201);

        $this->makeRegisterRequest($client, $this->validRegisterData);
        $this->assertResponseStatusCodeSame(409);
    }

    #[Group('user')]
    #[Group('registration')]
    public function testRegisterEmailNormalization(): void
    {
        $client = $this->createClient();

        $dataWithUppercaseEmail = $this->validRegisterData;
        $dataWithUppercaseEmail['email'] = 'TEST@TEST.COM';

        $this->makeRegisterRequest($client, $dataWithUppercaseEmail);
        self::assertResponseIsSuccessful();

        $responseData = $this->getJsonResponse($client);
        self::assertEquals('test@test.com', $responseData['data']['user']['email']);
    }

    #[Group('user')]
    #[Group('login')]
    #[DataProvider('loginProvider')]
    public function testLogin(array $data, int $expectedStatusCode): void
    {
        $client = static::createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);

        $this->makeLoginRequest($client, $data);
        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseData = json_decode($client->getResponse()->getContent(), true)['data'];
            $this->assertArrayHasKey('token', $responseData, 'Token should be present in the response');
            $this->assertArrayHasKey('user', $responseData, 'User data should be present in the response');
        }
    }

    #[Group('user')]
    #[Group('login')]
    public function testLoginWithDifferentEmailCase(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);
        self::assertResponseIsSuccessful();

        $uppercaseLoginData = $this->validLoginData;
        $uppercaseLoginData['email'] = strtoupper($this->validLoginData['email']);

        $this->makeLoginRequest($client, $uppercaseLoginData);
        self::assertResponseIsSuccessful();

        $responseData = $this->getJsonResponse($client);
        self::assertEquals('test@test.com', $responseData['data']['user']['email']);
    }

    #[Group('user')]
    #[Group('updateProfile')]
    #[DataProvider('updateProfileProvider')]
    public function testUpdateProfile(array $data, int $expectedStatusCode): void
    {
        $client = static::createClient();
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);
        self::assertNotEmpty($token, 'Token should not be empty after registration');
        $this->makeProfileUpdateRequest(
            $client,
            $data,
            $token
        );

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseData = json_decode($client->getResponse()->getContent(), true)['data'];
            $this->assertArrayHasKey('user', $responseData, 'User data should be present in the response');
        }
    }

    #[Group('user')]
    #[Group('updateProfile')]
    public function testUpdateProfileWithDuplicateEmail(): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $this->validRegisterData);

        $user2Data = [
            ...$this->validUpdateProfileData,
            'password' => 'password123',
            'confirmPassword' => 'password123',
        ];

        $this->makeRegisterRequest($client, $user2Data);
        $token2 = $this->getTokenFromResponse($client);

        $updateData = $this->validUpdateProfileData;
        $updateData['email'] = $this->validRegisterData['email'];

        $this->makeProfileUpdateRequest($client, $updateData, $token2);

        self::assertResponseStatusCodeSame(409);
    }

    #[Group('user')]
    #[Group('updateProfile')]
    public function testUpdateProfileEmailNormalization(): void
    {
        $client = $this->createClient();
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);

        $updateData = $this->validUpdateProfileData;
        $updateData['email'] = 'UPDATED@TEST.COM';

        $this->makeProfileUpdateRequest($client, $updateData, $token);
        self::assertResponseIsSuccessful();

        $responseData = $this->getJsonResponse($client);
        self::assertEquals('updated@test.com', $responseData['data']['user']['email']);
    }

    #[Group('user')]
    #[Group('profile')]
    public function testProfileSuccess(): void
    {
        $client = $this->createClient();
        $this->makeRegisterRequest($client, $this->validRegisterData);
        $token = $this->getTokenFromResponse($client);
        $this->makeProfileRequest($client, $token);

        self::assertResponseIsSuccessful();

        $responseData = $this->getJsonResponse($client);
        self::assertArrayHasKey('data', $responseData);
        self::assertArrayHasKey('user', $responseData['data']);
        self::assertArrayHasKey('roles', $responseData['data']['user']);
        self::assertContains('ROLE_USER', $responseData['data']['user']['roles']);
    }


    #[Group('user')]
    #[Group('authentication')]
    #[DataProvider('authenticationProvider')]
    public function testAuthentication(?string $token, int $expectedStatus): void
    {
        $client = $this->createClient();

        if ($token) {
            $client->setServerParameter('HTTP_Authorization', 'Bearer ' . $token);
        }

        $client->request('GET', '/api/profile');

        self::assertResponseStatusCodeSame($expectedStatus);
    }


    #[Group('user')]
    #[Group('http-methods')]
    #[DataProvider('httpMethodProvider')]
    public function testWrongHttpMethods(string $method, string $endpoint, array $allowedStatusCodes): void
    {
        $client = $this->createClient();

        $client->request($method, $endpoint);

        $statusCode = $client->getResponse()->getStatusCode();
        self::assertContains(
            $statusCode,
            $allowedStatusCodes,
            "Wrong method {$method} for {$endpoint} should return one of: " . implode(', ', $allowedStatusCodes)
        );
    }


    #[Group('user')]
    #[Group('content-type')]
    #[DataProvider('contentTypeProvider')]
    public function testContentType(string $method, string $endpoint, ?string $contentType, $expectedStatus): void
    {
        $client = $this->createClient();

        $headers = [];
        if ($contentType) {
            $headers['CONTENT_TYPE'] = $contentType;
        }

        $data = $endpoint === '/api/register' ? $this->validRegisterData : $this->validLoginData;

        $client->request($method, $endpoint, [], [], $headers, json_encode($data));

        if (is_array($expectedStatus)) {
            self::assertContains($client->getResponse()->getStatusCode(), $expectedStatus);
        } else {
            self::assertResponseStatusCodeSame($expectedStatus);
        }
    }


    #[Group('user')]
    #[Group('security')]
    #[DataProvider('securityProvider')]
    public function testSecurity(array $userData): void
    {
        $client = $this->createClient();

        $this->makeRegisterRequest($client, $userData);

        $statusCode = $client->getResponse()->getStatusCode();

        if ($statusCode === 201) {
            $responseData = $this->getJsonResponse($client);
            // Data should be stored as plain text, not executed
            self::assertEquals($userData['firstName'], $responseData['data']['user']['firstName']);
            self::assertEquals($userData['lastName'], $responseData['data']['user']['lastName']);
        } else {
            // Should fail validation due to invalid characters
            self::assertResponseStatusCodeSame(422);
        }
    }

    #[Group('user')]
    #[Group('performance')]
    public function testConcurrentRegistrations(): void
    {
        $client = $this->createClient();

        $startTime = microtime(true);

        for ($i = 1; $i <= 10; $i++) {
            $userData = $this->validRegisterData;
            $userData['email'] = "user{$i}@test.com";

            $this->makeRegisterRequest($client, $userData);
            self::assertResponseIsSuccessful();
        }

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        self::assertLessThan(10, $duration, "10 registrations took too long: {$duration} seconds");
    }
}