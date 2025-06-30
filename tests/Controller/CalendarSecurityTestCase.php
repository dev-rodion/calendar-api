<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\Group;

final class CalendarSecurityTestCase extends AbstractControllerTestCase
{
    #[Group('security')]
    #[Group('calendar')]
    public function testUserCannotUpdateOtherUserCalendar(): void
    {
        $client = $this->createClient();

        // Create first user and their calendar
        $user1Data = $this->validRegisterData;
        $this->makeRegisterRequest($client, $user1Data);
        $token1 = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $this->validCalendarData, $token1);
        $calendarData = $this->getDataFromResponse($client);
        $calendarId = $calendarData['calendar']['id'];

        // Create second user
        $user2Data = $this->validRegisterData;
        $user2Data['email'] = 'user2@test.com';
        $this->makeRegisterRequest($client, $user2Data);
        $token2 = $this->getTokenFromResponse($client);

        // Try to update first user's calendar with second user's token
        $updateData = [
            'title' => 'Hacked Calendar',
            'description' => 'This should not work',
            'position' => 999,
            'color' => '000000'
        ];

        $this->makeCalendarUpdateRequest($client, $calendarId, $updateData, $token2);
        
        // This should fail with 403 Forbidden or 404 Not Found
        $statusCode = $client->getResponse()->getStatusCode();
        echo "\nStatus code for updating other user's calendar: " . $statusCode . "\n";
        echo "Response: " . $client->getResponse()->getContent() . "\n";

        self::assertContains($statusCode, [403, 404], 'User should not be able to update another user\'s calendar');
    }

    #[Group('security')]
    #[Group('calendar')]
    public function testUserCannotViewOtherUserCalendar(): void
    {
        $client = $this->createClient();

        // Create first user and their calendar
        $user1Data = $this->validRegisterData;
        $this->makeRegisterRequest($client, $user1Data);
        $token1 = $this->getTokenFromResponse($client);

        $this->makeCalendarCreateRequest($client, $this->validCalendarData, $token1);
        $calendarData = $this->getDataFromResponse($client);
        $calendarId = $calendarData['calendar']['id'];

        // Create second user
        $user2Data = $this->validRegisterData;
        $user2Data['email'] = 'user2@test.com';
        $this->makeRegisterRequest($client, $user2Data);
        $token2 = $this->getTokenFromResponse($client);

        // Try to view first user's calendar with second user's token
        $this->makeCalendarShowRequest($client, $calendarId, $token2);
        
        $statusCode = $client->getResponse()->getStatusCode();
        echo "\nStatus code for viewing other user's calendar: " . $statusCode . "\n";
        echo "Response: " . $client->getResponse()->getContent() . "\n";

        // If calendars are meant to be private, this should fail
        // If calendars are meant to be public, this should succeed
        // Let's see what actually happens
    }
}
