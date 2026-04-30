<?php

namespace App\Application\Security;

use Random\RandomException;
use App\Domain\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTEncoderInterface $jwtEncoder,
        private readonly int $tokenTTL,
    ) {
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function isCredentialsValid(string $email, string $password): bool
    {
        $user = $this->userService->findUserByEmail($email);
        if ($user === null) {
            return false;
        }

        return $this->passwordHasher->isPasswordValid($user, $password);
    }

    /**
     * @param string $email
     * @return string
     * @throws JWTEncodeFailureException
     * @throws RandomException
     */
    public function getToken(string $email): string
    {
        $user = $this->userService->findUserByEmail($email);
        $refreshToken = $this->userService->updateUserRefreshToken($email);

        $tokenData = [
            'username' => $email,
            'roles' => $user?->getRoles() ?? [],
            'exp' => time() + $this->tokenTTL,
            'refresh_token' => $refreshToken,
        ];

        return $this->jwtEncoder->encode($tokenData);
    }
}
