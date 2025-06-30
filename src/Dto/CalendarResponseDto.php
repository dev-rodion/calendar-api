<?php

namespace App\Dto;

use App\Entity\Calendar;
use DateTimeImmutable;

class CalendarResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?int $position,
        public readonly ?string $color,
        public readonly string $owner,
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromEntity(Calendar $calendar): self
    {
        return new self(
            id: $calendar->getId(),
            title: $calendar->getTitle(),
            description: $calendar->getDescription(),
            position: $calendar->getPosition(),
            color: $calendar->getColor(),
            owner: $calendar->getOwner()->getEmail(),
            createdAt: $calendar->getCreatedAt(),
            updatedAt: $calendar->getUpdatedAt()
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'position' => $this->position,
            'color' => $this->color,
            'owner' => $this->owner,
            'createdAt' => $this->createdAt->format(DATE_ATOM),
            'updatedAt' => $this->updatedAt->format(DATE_ATOM),
        ];
    }
}
