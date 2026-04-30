<?php

namespace Unit\Application\Security;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\UserService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Application\Security\ApiTokenAuthenticator;
use App\Controller\Exception\AccessDeniedException;
use App\Controller\Exception\UnauthorizedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

#[CoversClass(ApiTokenAuthenticator::class)]
class ApiTokenAuthenticatorTest extends TestCase
{
    private UserService $userService;
    private JWTEncoderInterface $jwtEncoder;
    private ApiTokenAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->jwtEncoder = $this->createMock(JWTEncoderInterface::class);
        $this->authenticator = new ApiTokenAuthenticator($this->userService, $this->jwtEncoder);
    }

    #[Test]
    public function testSupportsReturnsTrue(): void
    {
        $this->assertTrue($this->authenticator->supports(new Request()));
    }

    #[Test]
    public function testAuthenticateThrowsUnauthorizedIfNoToken(): void
    {
        $request = new Request(); // Пустые заголовки

        $this->expectException(UnauthorizedException::class);
        $this->authenticator->authenticate($request);
    }

    #[Test]
    public function testAuthenticateThrowsUnauthorizedIfInvalidJwt(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer invalid_token');

        $this->jwtEncoder->method('decode')->willReturn([]); // Нет поля username

        $this->expectException(UnauthorizedException::class);
        $this->authenticator->authenticate($request);
    }

    #[Test]
    public function testAuthenticateThrowsAccessDeniedIfNoRefreshToken(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer valid_token');

        $this->jwtEncoder->method('decode')->willReturn(['username' => 'test_user']);

        $this->expectException(AccessDeniedException::class);
        $this->authenticator->authenticate($request);
    }

    #[Test]
    public function testAuthenticateSuccess(): void
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer valid_token');

        $tokenData = [
            'username' => 'test_user',
            'refresh_token' => 'valid_refresh_123'
        ];

        $this->jwtEncoder->method('decode')->willReturn($tokenData);

        $passport = $this->authenticator->authenticate($request);

        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);

        // Проверяем, что UserBadge содержит refresh_token
        $badge = $passport->getBadge(UserBadge::class);
        $this->assertEquals('valid_refresh_123', $badge->getUserIdentifier());

        // Проверяем вызов загрузчика пользователя
        $user = $this->createMock(User::class);

        $this->userService->expects($this->once())
            ->method('findUserByRefreshToken')
            ->with('valid_refresh_123')
            ->willReturn($user);

        $this->assertSame($user, $badge->getUser());
    }

    #[Test]
    public function testOnAuthenticationFailureThrowsException(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->authenticator->onAuthenticationFailure(
            new Request(),
            new AuthenticationException()
        );
    }
}
