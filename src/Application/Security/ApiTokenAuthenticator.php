<?php

namespace App\Application\Security;

use Exception;
use App\Domain\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\Exception\AccessDeniedException;
use App\Controller\Exception\UnauthorizedException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserService $userService,
        private readonly JWTEncoderInterface $jwtEncoder
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if (false === $token || null === $token) {
            throw new UnauthorizedException();
        }

        try {
            $tokenData = $this->jwtEncoder->decode($token);
        } catch (Exception $e) {
            throw new AuthenticationException('Invalid JWT format');
        }

        if (!isset($tokenData['username'])) {
            throw new UnauthorizedException();
        }

        $refreshToken = $tokenData['refresh_token'] ?? null;

        if ($refreshToken === null) {
            throw new AccessDeniedException();
        }

        return new SelfValidatingPassport(
            new UserBadge($refreshToken, fn(string $identifier) => $this->userService->findUserByRefreshToken($identifier))
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse([
            'message' => 'Invalid or missing token',
            'details' => $exception->getMessageKey()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
