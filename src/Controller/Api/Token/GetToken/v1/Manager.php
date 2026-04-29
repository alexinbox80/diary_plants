<?php

namespace App\Controller\Api\Token\GetToken\v1;

use App\Application\Security\AuthService;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Exception\AccessDeniedException;
use App\Controller\Exception\UnauthorizedException;

class Manager
{
    public function __construct(
        private readonly AuthService $authService
    ) {
    }

    /**
     * @throws AccessDeniedException
     * @throws UnauthorizedException
     */
    public function getToken(Request $request): string
    {
        $user = $request->getUser();
        $password = $request->getPassword();

        if (!$user || !$password) {
            throw new UnauthorizedException();
        }
        if (!$this->authService->isCredentialsValid($user, $password)) {
            throw new AccessDeniedException();
        }

        return $this->authService->getToken($user);
    }
}
