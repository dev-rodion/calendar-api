<?php

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\EmailIsTakenException;
use App\Exception\InvalidPasswordException;
use App\Exception\PasswordChangeException;
use App\Exception\SamePasswordException;
use App\Exception\TokenGenerationException;
use App\Exception\UserAuthenticationException;
use App\Exception\UserDeletionException;
use App\Exception\UserRegistrationException;
use App\Exception\UserUpdateException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly bool $isDebug = false
    ) {
    }

    public function registerUser(UserDto $userDto): User
    {
        if ($this->isEmailTaken($userDto->email) === true) {
            throw new EmailIsTakenException();
        }

        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $userDto->password));
        $user->setRoles(['ROLE_USER']);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new UserRegistrationException();
        }

        return $user;
    }

    public function authenticateUser(string $email, string $password): User
    {
        $user = $this->userRepository->findUserByEmail($email);

        if (!$user) {
            throw new UserAuthenticationException('User not found with the provided email.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new InvalidPasswordException('Invalid password provided.');
        }

        return $user;
    }

    public function updateUser(User $user, UserDto $userDto): void
    {
        if ($userDto->email !== $user->getEmail() && $this->isEmailTaken($userDto->email)) {
            throw new EmailIsTakenException();
        }

        if ($userDto->firstName !== $user->getFirstName()) {
            $user->setFirstName($userDto->firstName);
        }
        if ($userDto->lastName !== $user->getLastName()) {
            $user->setLastName($userDto->lastName);
        }

        $user->setEmail($userDto->email);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new UserUpdateException();
        }
    }

    public function updateUserPassword(User $user, UserDto $userDto): void
    {
        if (!$this->passwordHasher->isPasswordValid($user, $userDto->currentPassword)) {
            throw new InvalidPasswordException('Current password is incorrect');
        }

        if ($this->passwordHasher->isPasswordValid($user, $userDto->password)) {
            throw new SamePasswordException('New password must be different from current password');
        }

        // Устанавливаем новый пароль
        $hashedPassword = $this->passwordHasher->hashPassword($user, $userDto->password);
        $user->setPassword($hashedPassword);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new PasswordChangeException();
        }
    }

    public function deleteUser(User $user): void
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (Exception $e) {
            throw new UserDeletionException();
        }
    }

    public function isEmailTaken(string $email): bool
    {
        return $this->userRepository->findUserByEmail($email) !== null;
    }

    public function generateToken(User $user): string
    {
        try {
            return $this->jwtTokenManager->create($user);
        } catch (Exception $e) {
            throw new TokenGenerationException();
        }
    }
}