<?php

namespace Unit\Infrastructure\EventSubscriber;

use Twig\Environment;
use PHPUnit\Framework\TestCase;
use Twig\Loader\LoaderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Infrastructure\EventSubscriber\ExceptionSubscriber;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SecurityAccessDeniedException;

#[CoversClass(ExceptionSubscriber::class)]
class ExceptionSubscriberTest extends TestCase
{
    private $twig;
    private $subscriber;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->subscriber = new ExceptionSubscriber($this->twig);
    }

    #[Test]
    public function testOnKernelExceptionReturnsJsonResponseForApi(): void
    {
        // 1. Подготовка (создаем событие с исключением доступа и путем /api)
        $exception = new SecurityAccessDeniedException('Forbidden message');
        $request = Request::create('/api/data');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // 2. Действие
        $this->subscriber->onKernelException($event);

        // 3. Проверка
        $response = $event->getResponse();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('Forbidden message', $data['message']);
    }

    #[Test]
    public function testOnKernelExceptionReturnsHtmlResponseForBrowser(): void
    {
        // 1. Подготовка (обычный запрос, имитируем наличие шаблона)
        $exception = new \Exception('Standard error');
        $request = Request::create('/');

        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('exists')->willReturn(true);
        $this->twig->method('getLoader')->willReturn($loader);

        $this->twig->expects($this->once())
            ->method('render')
            ->with($this->stringContains('error500.html.twig'))
            ->willReturn('<html>Error Page</html>');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // 2. Действие
        $this->subscriber->onKernelException($event);

        // 3. Проверка
        $response = $event->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('<html>Error Page</html>', $response->getContent());
    }

    #[Test]
    public function testMapExceptionToCodeWithGenericException(): void
    {
        $request = Request::create('/api/test');
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new \RuntimeException('Boom')
        );

        $this->subscriber->onKernelException($event);

        $this->assertEquals(500, $event->getResponse()->getStatusCode());
    }
}
