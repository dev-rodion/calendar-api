<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Dto\UserResponseDto;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * UserController handles user-related actions such as registration, login, profile management, and admin operations.
 *
 * @route POST /register - User registration
 * @route POST /login - User login
 * @route GET /profile - Fetch user profile
 * @route PUT /profile - Update user profile
 * @route PATCH /profile/password - Update user password
 * @route DELETE /profile - Delete user profile
 */
final class UserController extends BaseController
{
    #[Route('/register', name: 'app_user_register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload(validationGroups: ['register'])] UserDto $userDto,
        UserService $userService
    ): JsonResponse {
        $user = $userService->registerUser($userDto);
        $token = $userService->generateToken($user);

        return $this->successResponse(
            [
                'user' => UserResponseDto::fromEntity($user),
                'token' => $token
            ],
            'User registered successfully',
            201
        );
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload(validationGroups: ['login'])] UserDto $userDto,
        UserService $userService
    ): JsonResponse {
        $user = $userService->authenticateUser($userDto->email, $userDto->password);
        $token = $userService->generateToken($user);

        return $this->successResponse(
            [
                'user' => UserResponseDto::fromEntity($user),
                'token' => $token
            ],
            'User logged in successfully'
        );
    }

    #[Route('/profile', name: 'app_user_get', methods: ['GET'])]
    public function getUserInfo(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();

        return $this->successResponse([
            'user' => UserResponseDto::fromEntity($user)
        ], 'User profile fetched successfully');
    }

    #[Route('/profile', name: 'app_user_update', methods: ['PUT'])]
    public function updateUser(
        #[MapRequestPayload(validationGroups: ['update'])] UserDto $userDto,
        UserService $userService
    ): JsonResponse {
        $user = $this->getAuthenticatedUser();
        $userService->updateUser($user, $userDto);

        return $this->successResponse([
            'user' => UserResponseDto::fromEntity($user)
        ], 'Profile updated successfully');
    }

    #[Route('/profile/password', name: 'app_user_update_password', methods: ['PATCH'])]
    public function patchUser(
        #[MapRequestPayload(validationGroups: ['update_password'])] UserDto $userDto,
        UserService $userService
    ): JsonResponse {
        $user = $this->getAuthenticatedUser();
        $userService->updateUserPassword($user, $userDto);
        $newToken = $userService->generateToken($user);

        return $this->successResponse([
            'user' => UserResponseDto::fromEntity($user),
            'token' => $newToken
        ], 'Password updated successfully');
    }

    #[Route('/profile', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(
        UserService $userService
    ): JsonResponse {
        $user = $this->getAuthenticatedUser();
        $userService->deleteUser($user);
        return $this->successResponse(
            status: 204
        );
    }

    // === ADMIN ONLY ROUTES ===

    #[Route('/users', name: 'app_user_list', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        // List users logic here
        $users = []; // This should be replaced with actual user data fetching logic
        return $this->successResponse(['users' => $users], 'Users listed successfully');
    }

    #[Route('/users/{id}', name: 'app_user_get_by_id', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        // Fetch user by ID logic here
        $user = []; // This should be replaced with actual user data fetching logic by ID
        return $this->successResponse(['user' => $user], 'User fetched successfully');
    }

    #[Route('/users/{id}', name: 'app_user_update_by_id', methods: ['PUT'])]
    public function updateUserById(int $id): JsonResponse
    {
        // Update user by ID logic here
        $user = []; // This should be replaced with actual user data updating logic by ID
        return $this->successResponse(['user' => $user], 'User updated successfully');
    }

    #[Route('/users/{id}', name: 'app_user_delete_by_id', methods: ['DELETE'])]
    public function deleteUserById(int $id): JsonResponse
    {
        // Delete user by ID logic here
        return $this->successResponse([], 'User deleted successfully', 204);
    }

}
