<?php

namespace App\Controller;

use App\Dto\CalendarDto;
use App\Dto\CalendarResponseDto;
use App\Service\CalendarService;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * CalendarController handles calendar-related requests.
 *
 * @route GET /calendars - List all user calendars
 * @route POST /calendars - Create a new calendar
 * @route GET /calendars/{id} - Get a specific calendar
 * @route PUT /calendars/{id} - Update a specific calendar
 * @route DELETE /calendars/{id} - Delete a specific calendar
 */
final class CalendarController extends BaseController
{
    #[Route('/calendars', name: 'calendar_index', methods: ['GET'])]
    public function index(
        CalendarService $calendarService
    ): JsonResponse {
        $calendars = $calendarService->getAllUserCalendars($this->getUser());
        return $this->successResponse([
            'calendars' => $calendars
        ]);
    }

    #[Route('/calendars', name: 'calendar_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(validationGroups: ['create'])] CalendarDto $data,
        CalendarService $calendarService
    ): JsonResponse {
        $user = $this->getAuthenticatedUser();
        $calendar = $calendarService->createCalendar($user, $data);

        return $this->successResponse([
            'calendar' => CalendarResponseDto::fromEntity($calendar)
        ], 'Calendar created successfully', 201);
    }

    #[Route('/calendars/{id}', name: 'calendar_show', methods: ['GET'])]
    public function show(int $id, CalendarService $calendarService): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $calendar = $calendarService->showCalendar($user, $id);

        return $this->successResponse([
            'calendar' => CalendarResponseDto::fromEntity($calendar)
        ], 'Calendar retrieved successfully');
    }

    #[Route('/calendars/{id}', name: 'calendar_update', methods: ['PUT'])]
    public function update(
        int $id,
        #[MapRequestPayload(validationGroups: ['update'])] CalendarDto $data,
        CalendarService $calendarService
    ): JsonResponse {
        $user = $this->getAuthenticatedUser();
        $calendar = $calendarService->updateCalendar($user, $id, $data);

        return $this->successResponse([
            'calendar' => CalendarResponseDto::fromEntity($calendar)
        ], 'Calendar updated successfully');
    }

    #[Route('/calendars/{id}', name: 'calendar_delete', methods: ['DELETE'])]
    public function delete(int $id, CalendarService $calendarService): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $calendarService->deleteCalendar($user, $id);

        return $this->successResponse([], 'Calendar deleted successfully', 204);
    }
}