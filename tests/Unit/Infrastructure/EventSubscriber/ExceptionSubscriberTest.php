<?php

namespace Unit\Infrastructure\EventSubscriber;

use Exception;
use RuntimeException;
use Twig\Environment;
use PHPUnit\Framework\TestCase;
use Twig\Loader\LoaderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Infrastructure\EventSubscriber\ExceptionSubscriber;

#[CoversClass(ExceptionSubscriber::class)]
class ExceptionSubscriberTest extends TestCase
{
    private $twig;
    private $subscriber;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->subscriber = new ExceptionSubscriber($this->twig, true);
    }

    #[Test]
    public function testOnKernelExceptionReturnsJsonResponseForApi(): void
    {
        // 1. Подготовка: создаем исключение и имитируем API запрос
        // Используем RuntimeException, чтобы получить статус 500 по умолчанию
        $exception = new RuntimeException('Database connection failed');
        $request = Request::create('/api/v1/plants');
        $request->headers->set('Accept', 'application/json');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // 2. Действие: вызываем обработчик
        $this->subscriber->onKernelException($event);

        // 3. Проверка ответа
        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        // Проверка структуры JSON
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('error', $data['status']);

        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Database connection failed', $data['message']);

        // Проверка детализации (так как в setUp мы передали debug = true)
        $this->assertArrayHasKey('debug', $data, 'В режиме debug должен присутствовать ключ debug');
        $this->assertArrayHasKey('file', $data['debug']);
        $this->assertArrayHasKey('line', $data['debug']);
        $this->assertArrayHasKey('trace', $data['debug']);

        // Проверяем, что в trace действительно массив (стек вызовов)
        $this->assertIsArray($data['debug']['trace']);
    }

    #[Test]
    public function testOnKernelExceptionReturnsHtmlResponseForBrowser(): void
    {
        // 1. Подготовка (обычный запрос, имитируем наличие шаблона)
        $exception = new Exception('Standard error');
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
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
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
            new RuntimeException('Boom')
        );

        $this->subscriber->onKernelException($event);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
    }
}
