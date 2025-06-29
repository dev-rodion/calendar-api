<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Summary of UserDto
 * 
 * Register: 
 * @property string $firstName First name of the user
 * @property string|null $lastName Last name of the user
 * @property string $email Email address of the user
 * @property string $password Password for the user
 * @property string $confirmPassword Confirm password for the user
 * 
 * Login:
 * @property string $email Email address of the user
 * @property string $password Password for the user
 * 
 * Update:
 * @property string $firstName First name of the user
 * @property string|null $lastName Last name of the user
 * @property string $email Email address of the user
 * @property string|null $role Role of the user
 * 
 * Update Password:
 * @property string $currentPassword Current password for the user, used for updating profile
 * @property string $password Password for the user
 * @property string $confirmPassword Confirm password for the user
 */
class UserDto
{
    /**
     * @var string|null First name of the user
     */
    #[Assert\NotBlank(groups: ['register', 'update'], message: 'First name cannot be blank.')]
    #[Assert\Length(min: 3, max: 50, groups: ['register', 'update'], minMessage: 'First name must be at least 3 characters long.', maxMessage: 'First name cannot exceed 50 characters.')]
    public ?string $firstName = null;

    /**
     * @var string|null Last name of the user
     */
    #[Assert\Length(min: 3, max: 50, groups: ['register', 'update'], minMessage: 'Last name must be at least 3 characters long.', maxMessage: 'Last name cannot exceed 50 characters.')]
    public ?string $lastName = null;

    /**
     * @var string|null Email address of the user
     */
    #[Assert\NotBlank(groups: ['register', 'login', 'update'], message: 'Email cannot be blank.')]
    #[Assert\Email(groups: ['register', 'login', 'update'], message: 'Invalid email format.')]
    #[Assert\Length(max: 180, groups: ['register', 'login', 'update'], maxMessage: 'Email cannot exceed 180 characters.')]
    public ?string $email = null;

    /**
     * @var string|null Role of the user
     */
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], groups: ['update'], message: 'Invalid role.')]
    public ?string $role = null;

    /**
     * @var string|null Current password for the user, used for updating profile
     */
    #[Assert\NotBlank(groups: ['update_password'], message: 'Current password cannot be blank.')]
    #[Assert\NotEqualTo(
        propertyPath: 'password',
        groups: ['update_password'],
        message: 'Current password cannot be the same as the new password.'
    )]
    public ?string $currentPassword = null;

    /**
     * @var string|null Password for the user
     */
    #[Assert\NotBlank(groups: ['register', 'login', 'update_password'], message: 'Password cannot be blank.')]
    #[Assert\Length(min: 6, max: 100, groups: ['register', 'login', 'update_password'], minMessage: 'Password must be at least 6 characters long.', maxMessage: 'Password cannot exceed 100 characters.')]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{6,}$/',
        message: 'Password must contain at least one letter, one number, and be at least 6 characters long.',
        groups: ['register', 'login', 'update_password']
    )]
    public ?string $password = null;

    /**
     * @var string|null Confirm password for the user
     */
    #[Assert\NotBlank(groups: ['register', 'update_password'], message: 'Confirm password cannot be blank.')]
    #[Assert\EqualTo(propertyPath: 'password', groups: ['register', 'update_password'], message: 'Passwords must match.')]
    public ?string $confirmPassword = null;

}
