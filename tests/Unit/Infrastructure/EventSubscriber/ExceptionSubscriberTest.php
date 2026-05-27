<?php

namespace Unit\Infrastructure\EventSubscriber;

use stdClass;
use Exception;
use RuntimeException;
use Twig\Environment;
use PHPUnit\Framework\TestCase;
use Twig\Loader\LoaderInterface;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\IncidentService;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Infrastructure\EventSubscriber\ExceptionSubscriber;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(ExceptionSubscriber::class)]
class ExceptionSubscriberTest extends TestCase
{
    private $twig;
    private $incidentService;
    private $subscriber;
    private $translator;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->incidentService = $this->createMock(IncidentService::class);

        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->translator->method('trans')->willReturnArgument(0);

        $this->subscriber = new ExceptionSubscriber($this->twig, true, $this->incidentService, $this->translator);
    }

    #[Test]
    public function testOnKernelExceptionReturnsJsonResponseForApi(): void
    {
        $exception = new RuntimeException('Database connection failed');
        $request = Request::create('/api/v1/plants');
        $request->headers->set('Accept', 'application/json');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('error', $data['status']);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals('Database connection failed', $data['message']);
        $this->assertArrayHasKey('debug', $data);
    }

    #[Test]
    public function testOnKernelExceptionReturnsHtmlResponseForBrowser(): void
    {
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

        $this->subscriber->onKernelException($event);

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

    #[Test]
    public function testOnKernelExceptionLogsIncidentOnProduction(): void
    {
        // Пересоздаем подписчик для режима продакшена (debug = false)
        $subscriber = new ExceptionSubscriber($this->twig, false, $this->incidentService, $this->translator);

        $exception = new RuntimeException('Critical DB Error');
        $request = Request::create('/api/v1/data');
        $request->headers->set('Accept', 'application/json');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // Настраиваем переводчик на выдачу правильной фразы при вызове ключа 5xx ошибки
        $this->translator->expects($this->once())
            ->method('trans')
            ->with('exception.subscriber.5xx.json')
            ->willReturn('Внутренняя ошибка сервера. Пожалуйста, передайте код ошибки в службу поддержки.');

        $this->incidentService->expects($this->once())
            ->method('createFromException')
            ->with($exception, $request, Response::HTTP_INTERNAL_SERVER_ERROR)
            ->willReturn('INC-12345');

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        $data = json_decode($response->getContent(), true);

        $this->assertEquals('exception.subscriber.5xx.json', $data['message']);
        $this->assertArrayHasKey('error_code', $data);
        $this->assertEquals('INC-12345', $data['error_code']);
    }

    #[Test]
    public function testOnKernelExceptionHandlesValidationFailedException(): void
    {
        // Имитируем ошибку валидации поля "email"
        $violation = new ConstraintViolation('Неверный формат email', null, [], null, 'email', null);
        $violations = new ConstraintViolationList([$violation]);

        // Обязательно используем ПОЛНЫЙ путь к классу валидатора, чтобы избежать путаницы
        $exception = new ValidationFailedException(new stdClass(), $violations);

        $request = Request::create('/api/v1/user', 'POST');
        $request->headers->set('Accept', 'application/json');

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->subscriber->onKernelException($event);

        $response = $event->getResponse();

        // Теперь здесь гарантированно вернется 400!
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $data);
        $this->assertEquals('Неверный формат email', $data['errors']['email']);
    }
}
