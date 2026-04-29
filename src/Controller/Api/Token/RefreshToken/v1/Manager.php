<?php

namespace App\Controller\Api\Token\RefreshToken\v1;

use App\Domain\Service\UserService;
use App\Application\Security\AuthService;
use Symfony\Component\Security\Core\User\UserInterface;

class Manager
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly UserService $userService,
    ) {
    }

    public function refreshToken(UserInterface $user): string
    {
        $this->userService->clearUserRefreshToken($user->getUserIdentifier());

        return $this->authService->getToken($user->getUserIdentifier());
    }
}
