<?php

namespace App\Tests\DataProvider;

use Generator;

class CalendarDataProvider
{
    public static function createCalendarProvider(): Generator
    {
        yield 'valid calendar creation' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'FF5733',
            ],
            201
        ];

        yield 'empty title' => [
            [
                'title' => 'A',
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'FF5733',
            ],
            422
        ];

        yield 'null title' => [
            [
                'title' => null,
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'FF5733',
            ],
            422
        ];

        yield 'too short title' => [
            [
                'title' => '',
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'FF5733',
            ],
            422
        ];

        yield 'too long title' => [
            [
                'title' => str_repeat('A', 51),
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'FF5733',
            ],
            422
        ];

        yield 'too long description' => [
            [
                'title' => 'Test Calendar',
                'description' => str_repeat('A', 256),
                'position' => 1,
                'color' => 'FF5733',
            ],
            422
        ];

        yield 'invalid position negative' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => -1,
                'color' => 'FF5733',
            ],
            201
        ];

        yield 'invalid position zero' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => 0,
                'color' => 'FF5733',
            ],
            201
        ];

        yield 'invalid color format' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => 1,
                'color' => 'invalid-color',
            ],
            422
        ];

        yield 'empty color' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => 1,
                'color' => '',
            ],
            422
        ];

        yield 'null color' => [
            [
                'title' => 'Test Calendar',
                'description' => 'Test Description',
                'position' => 1,
                'color' => null,
            ],
            201
        ];

        yield 'empty data' => [[], 422];
    }

    public static function updateCalendarProvider(): Generator
    {
        yield 'valid calendar update' => [
            [
                'title' => 'Updated Calendar',
                'description' => 'Updated Description',
                'position' => 2,
                'color' => '33FF57',
            ],
            200
        ];

        yield 'empty title update' => [
            [
                'title' => '',
                'description' => 'Updated Description',
                'position' => 2,
                'color' => '33FF57',
            ],
            422
        ];

        yield 'invalid color update' => [
            [
                'title' => 'Updated Calendar',
                'description' => 'Updated Description',
                'position' => 2,
                'color' => 'invalid-color',
            ],
            422
        ];

        yield 'empty data update' => [[], 422];
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
        yield 'PUT create calendar' => ['PUT', '/api/calendars', [404, 405, 500]];
        yield 'DELETE create calendar' => ['DELETE', '/api/calendars', [404, 405, 500]];
        yield 'POST show calendar' => ['POST', '/api/calendars/1', [404, 405, 500]];
        yield 'POST update calendar' => ['POST', '/api/calendars/1', [404, 405, 500]];
    }

    public static function contentTypeProvider(): Generator
    {
        yield 'no content type create' => ['POST', '/api/calendars', null, 400];
        yield 'wrong content type create' => ['POST', '/api/calendars', 'text/plain', [400, 415, 500]];
        yield 'no content type update' => ['PUT', '/api/calendars/1', null, 400];
        yield 'wrong content type update' => ['PUT', '/api/calendars/1', 'text/plain', [400, 415, 500]];
    }

    public static function securityProvider(): Generator
    {
        yield 'xss in title and description' => [
            [
                'title' => '<script>alert("XSS")</script>',
                'description' => '<img src="x" onerror="alert(1)">',
                'position' => 1,
                'color' => 'FF5733',
            ]
        ];

        yield 'sql injection in title and description' => [
            [
                'title' => "'; DROP TABLE calendars; --",
                'description' => "1' OR '1'='1",
                'position' => 1,
                'color' => 'FF5733',
            ]
        ];
    }

    public static function ownershipProvider(): Generator
    {
        yield 'access own calendar' => [true, 200];
        yield 'access other user calendar' => [false, 403];
    }

    public static function updateOwnershipProvider(): Generator
    {
        yield 'update own calendar' => [true, 200];
        yield 'update other user calendar' => [false, 403];
    }

    public static function notFoundProvider(): Generator
    {
        yield 'calendar not exists' => [999, 404];
        yield 'negative calendar id' => [-1, 404];
        yield 'zero calendar id' => [0, 404];
    }

    public static function paginationProvider(): Generator
    {
        yield 'default pagination' => [null, null, 10];
        yield 'page 1 limit 5' => [1, 5, 5];
        yield 'page 2 limit 3' => [2, 3, 3];
        yield 'invalid page negative' => [-1, 10, 10];
        yield 'invalid limit zero' => [1, 0, 10];
        yield 'invalid limit negative' => [1, -5, 10];
        yield 'limit too high' => [1, 1000, 100];
    }
}
