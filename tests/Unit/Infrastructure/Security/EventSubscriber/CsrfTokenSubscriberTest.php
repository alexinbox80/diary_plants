<?php

namespace Unit\Infrastructure\Security\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Infrastructure\Security\EventSubscriber\CsrfTokenSubscriber;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CsrfTokenSubscriberTest extends TestCase
{
    private $tokenManager;
    private $subscriber;

    protected function setUp(): void
    {
        $this->tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->subscriber = new CsrfTokenSubscriber($this->tokenManager);
    }

    public function testSkipsValidationForGetRequest(): void
    {
        $event = $this->createControllerEvent('/api/v1/dashboard/stats', 'GET');

        // Ожидаем, что менеджер токенов НЕ будет вызван
        $this->tokenManager->expects($this->never())->method('isTokenValid');

        $this->subscriber->onKernelController($event);
    }

    public function testThrowsExceptionOnInvalidToken(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_X-CSRF-TOKEN' => 'wrong_token']);
        $request->setMethod('POST');
        $request->server->set('REQUEST_URI', '/api/v1/dashboard/save');

        $event = $this->createControllerEventByRequest($request);

        // Настраиваем мок: любой токен считаем невалидным
        $this->tokenManager->method('isTokenValid')->willReturn(false);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Invalid or missing CSRF token.');

        $this->subscriber->onKernelController($event);
    }

    public function testAllowsValidToken(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_X-CSRF-TOKEN' => 'valid_token']);
        $request->setMethod('POST');
        $request->server->set('REQUEST_URI', '/api/v1/dashboard/update');

        $event = $this->createControllerEventByRequest($request);

        // Имитируем успешную проверку
        $this->tokenManager->method('isTokenValid')
            ->with($this->callback(fn(CsrfToken $token) => $token->getValue() === 'valid_token'))
            ->willReturn(true);

        $this->subscriber->onKernelController($event);
        $this->assertTrue(true); // Если нет исключения, тест пройден
    }

    // Вспомогательные методы для создания события
    private function createControllerEvent(string $path, string $method): ControllerEvent
    {
        $request = Request::create($path, $method);
        return $this->createControllerEventByRequest($request);
    }

    private function createControllerEventByRequest(Request $request): ControllerEvent
    {
        return new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            function() {},
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
