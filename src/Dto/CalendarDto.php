<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CalendarDto
{
    #[Assert\NotBlank(groups: ['create', 'update'], message: 'Title cannot be blank.')]
    #[Assert\Length(min: 3, max: 100, groups: ['create', 'update'], minMessage: 'Title must be at least 3 characters long.', maxMessage: 'Title cannot exceed 100 characters.')]
    public ?string $title = null;

    #[Assert\Length(max: 255, groups: ['create', 'update'], maxMessage: 'Description cannot exceed 255 characters.')]
    public ?string $description = null;

    // #[Assert\Positive(groups: ['create', 'update'], message: 'Position must be a positive integer.')]
    public ?int $position = 0;

    #[Assert\Length(min: 6, max: 6, groups: ['create', 'update'], minMessage: 'Color must be exactly 6 characters long.', maxMessage: 'Color must be exactly 6 characters long.')]
    public ?string $color = null;

    // #[Assert\NotBlank(groups: ['update'], message: 'Owner cannot be blank.')]
    // public ?string $owner = null;
}