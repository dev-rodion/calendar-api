<?php

namespace App\Controller;

use App\Exception\UserUnauthorizedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

abstract class BaseController extends AbstractController
{
    protected function getAuthenticatedUser(): ?object
    {
        $user = $this->getUser();

        if (!$user) {
            throw new UserUnauthorizedException();
        }

        return $user;
    }

    protected function successResponse(array $data = [], string $message = 'OK', int $status = 200): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => $message,
                'data' => $data,
            ],
            $status
        );
    }

    protected function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => [
                    'message' => $message,
                    'code' => $status,
                ],
            ],
            $status
        );
    }
}
