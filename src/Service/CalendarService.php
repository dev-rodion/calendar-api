<?php

namespace App\Service;

use App\Dto\CalendarDto;
use App\Entity\Calendar;
use App\Entity\User;
use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalendarService
{
    public function __construct(
        private readonly CalendarRepository $calendarRepo,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function getAllUserCalendars(User $user): array
    {
        $calendars = $user->getCalendars();
        if ($calendars->isEmpty()) {
            // Logic to handle empty calendars, e.g., return an empty array or throw an exception
            return [];
        }
        return $calendars->toArray();
    }

    public function createCalendar(User $user, CalendarDto $data): Calendar
    {
        $calendar = new Calendar();
        $calendar->setTitle($data->title);
        $calendar->setDescription($data->description);
        $calendar->setPosition($data->position ?? 0); // Default position if not provided
        $calendar->setColor($data->color ?? null); // Default color if not provided
        $calendar->setOwner($user);

        $this->calendarRepo->save($calendar, true);

        return $calendar;
    }

    public function updateCalendar(User $user, int $id, CalendarDto $data): Calendar
    {
        $calendar = $this->calendarRepo->find($id);
        if (!$calendar) {
            throw new NotFoundHttpException('Calendar not found');
        }

        if ($calendar->getOwner() !== $user) {
            throw new AccessDeniedHttpException('You do not have permission to update this calendar');
        }

        $calendar->setTitle($data->title);
        $calendar->setDescription($data->description);
        $calendar->setPosition($data->position);
        $calendar->setColor($data->color);

        $this->em->persist($calendar);
        $this->em->flush();

        return $calendar;
    }

    public function showCalendar(User $user, int $id): Calendar
    {
        $calendar = $this->calendarRepo->find($id);
        if (!$calendar) {
            throw new NotFoundHttpException('Calendar not found');
        }

        if ($calendar->getOwner() !== $user) {
            throw new AccessDeniedHttpException('You do not have permission to view this calendar');
        }

        return $calendar;
    }

    public function getCalendarById(int $id): Calendar
    {
        $calendar = $this->calendarRepo->find($id);
        if (!$calendar) {
            throw new NotFoundHttpException('Calendar not found');
        }
        return $calendar;
    }

    public function deleteCalendar(User $user, int $id): void
    {
        $calendar = $this->getCalendarById($id);

        if ($calendar->getOwner() !== $user) {
            throw new AccessDeniedHttpException('You do not have permission to delete this calendar');
        }

        $this->em->remove($calendar);
        $this->em->flush();
    }
}