<?php

namespace App\Tests\DataProvider;

use Generator;

class UserDataProvider
{
    public static function registrationProvider(): Generator
    {
        yield 'valid registration' => [
            [
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            201
        ];

        yield 'empty first name' => [
            [
                'firstName' => '',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'null first name' => [
            [
                'firstName' => null,
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'too short first name' => [
            [
                'firstName' => 'A',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'too long first name' => [
            [
                'firstName' => str_repeat('A', 51),
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'invalid first name characters' => [
            [
                'firstName' => 'Test123',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'password mismatch' => [
            [
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'different',
            ],
            422
        ];

        yield 'invalid email' => [
            [
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => 'invalid-email',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ],
            422
        ];

        yield 'too short password' => [
            [
                'firstName' => 'Test',
                'lastName' => 'User',
                'email' => 'test@test.com',
                'password' => '12345',
                'confirmPassword' => '12345',
            ],
            422
        ];

        yield 'empty data' => [[], 422];
    }

    public static function loginProvider(): Generator
    {
        yield 'valid login' => [
            ['email' => 'test@test.com', 'password' => 'password123'],
            200
        ];

        yield 'wrong email' => [
            ['email' => 'wrong@test.com', 'password' => 'password123'],
            404
        ];

        yield 'wrong password' => [
            ['email' => 'test@test.com', 'password' => 'wrong'],
            401
        ];

        yield 'empty credentials' => [[], 422];

        yield 'invalid email format' => [
            ['email' => 'invalid-email', 'password' => 'password123'],
            422
        ];

        yield 'null email' => [
            ['email' => null, 'password' => 'password123'],
            422
        ];

        yield 'empty email' => [
            ['email' => '', 'password' => 'password123'],
            422
        ];

        yield 'sql injection attempt' => [
            ['email' => "'; DROP TABLE users; --", 'password' => 'password123'],
            422
        ];
    }

    public static function updateProfileProvider(): Generator
    {
        yield 'valid update' => [
            [
                'firstName' => 'Updated',
                'lastName' => 'Name',
                'email' => 'updated@test.com',
            ],
            200
        ];

        yield 'empty first name' => [
            [
                'firstName' => '',
                'lastName' => 'Name',
                'email' => 'updated@test.com',
            ],
            422
        ];

        yield 'invalid email' => [
            [
                'firstName' => 'Updated',
                'lastName' => 'Name',
                'email' => 'invalid-email',
            ],
            422
        ];

        yield 'empty data' => [[], 400];
    }

    public static function authenticationProvider(): Generator
    {
        yield 'no token' => [null, 403];
        yield 'invalid token' => ['invalid-token', 401];
        yield 'expired token' => ['eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2NzA0MDcyMDAsImV4cCI6MTY3MDQwNzIwMCwidXNlcm5hbWUiOiJ0ZXN0QHRlc3QuY29tIn0.invalid', 401];
        yield 'malformed token' => ['this-is-not-a-jwt-token', 401];
    }

    public static function httpMethodProvider(): Generator
    {
        yield 'GET register' => ['GET', '/api/register', [404, 405, 500]];
        yield 'PUT register' => ['PUT', '/api/register', [404, 405, 500]];
        yield 'DELETE register' => ['DELETE', '/api/register', [404, 405, 500]];
        yield 'GET login' => ['GET', '/api/login', [404, 405, 500]];
        yield 'PUT login' => ['PUT', '/api/login', [404, 405, 500]];
        yield 'DELETE login' => ['DELETE', '/api/login', [404, 405, 500]];
        yield 'GET logout' => ['GET', '/api/logout', [404, 405, 500]];
        yield 'PUT logout' => ['PUT', '/api/logout', [404, 405, 500]];
        yield 'DELETE logout' => ['DELETE', '/api/logout', [404, 405, 500]];
        yield 'POST profile' => ['POST', '/api/profile', [404, 405, 500]];
        yield 'PATCH profile' => ['PATCH', '/api/profile', [404, 405, 500]];
    }

    public static function contentTypeProvider(): Generator
    {
        yield 'no content type register' => ['POST', '/api/register', null, 400];
        yield 'wrong content type register' => ['POST', '/api/register', 'text/plain', [400, 415, 500]];
        yield 'no content type login' => ['POST', '/api/login', null, 400];
        yield 'wrong content type login' => ['POST', '/api/login', 'text/plain', [400, 415, 500]];
    }

    public static function securityProvider(): Generator
    {
        yield 'xss in names' => [
            [
                'firstName' => '<script>alert("XSS")</script>',
                'lastName' => '<img src="x" onerror="alert(1)">',
                'email' => 'xss_test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ]
        ];

        yield 'sql injection in names' => [
            [
                'firstName' => "'; DROP TABLE users; --",
                'lastName' => "1' OR '1'='1",
                'email' => 'sql_test@test.com',
                'password' => 'password123',
                'confirmPassword' => 'password123',
            ]
        ];
    }
}