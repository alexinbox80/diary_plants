<?php

namespace App\Application\Security;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[CoversClass(AuthServiceTest::class)]
class AuthServiceTest extends TestCase
{
    private UserService $userService;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTEncoderInterface $jwtEncoder;
    private AuthService $authService;
    private int $tokenTTL = 3600;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);

        $this->authService = new AuthService(
            $this->userService,
            $this->passwordHasher,
            $this->jwtEncoder,
            $this->tokenTTL
        );
    }

    #[Test]
    public function testIsCredentialsValidReturnsFalseIfUserNotFound(): void
    {
        $this->userService->method('findUserByEmail')->willReturn(null);

        $result = $this->authService->isCredentialsValid('test@example.com', 'password');

        $this->assertFalse($result);
    }

    #[Test]
    public function testIsCredentialsValidReturnsTrueOnSuccess(): void
    {
        $user = $this->createMock(User::class);
        $this->userService->method('findUserByEmail')->willReturn($user);
        $this->passwordHasher->method('isPasswordValid')->with($user, 'password')->willReturn(true);

        $result = $this->authService->isCredentialsValid('test@example.com', 'password');

        $this->assertTrue($result);
    }

    #[Test]
    public function testGetTokenGeneratesValidJwt(): void
    {
        $email = 'test@example.com';
        $roles = ['ROLE_USER'];
        $refreshToken = 'generated_refresh_token_123';
        $expectedJwt = 'header.payload.signature';

        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn($roles);

        $this->userService->method('findUserByEmail')->with($email)->willReturn($user);
        $this->userService->method('updateUserRefreshToken')->with($email)->willReturn($refreshToken);

        // Проверяем, что в энкодер передаются правильные ключи
        $this->jwtEncoder->expects($this->once())
            ->method('encode')
            ->with($this->callback(function (array $data) use ($email, $roles, $refreshToken) {
                return $data['username'] === $email &&
                    $data['roles'] === $roles &&
                    $data['refresh_token'] === $refreshToken &&
                    isset($data['exp']);
            }))
            ->willReturn($expectedJwt);

        $token = $this->authService->getToken($email);

        $this->assertEquals($expectedJwt, $token);
    }
}
