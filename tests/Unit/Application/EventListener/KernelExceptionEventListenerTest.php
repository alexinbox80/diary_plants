<?php

namespace Unit\Application\EventListener;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Controller\Exception\HttpCompliantExceptionInterface;
use App\Application\EventListener\KernelExceptionEventListener;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(KernelExceptionEventListener::class)]
class KernelExceptionEventListenerTest extends TestCase
{
    private KernelExceptionEventListener $listener;

    protected function setUp(): void
    {
        $this->listener = new KernelExceptionEventListener();
    }

    #[Test]
    public function testOnKernelExceptionHandleHttpCompliantException(): void
    {
        // Создаем анонимный класс, который имитирует ваше исключение
        $exception = new class extends \Exception implements HttpCompliantExceptionInterface {
            public function getHttpResponseBody(): string { return 'Custom Error'; }
            public function getHttpCode(): int { return Response::HTTP_FORBIDDEN; }
        };

        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Custom Error']),
            $response->getContent()
        );
    }

    #[Test]
    public function testOnKernelExceptionHandleValidationFailedException(): void
    {
        // Создаем список нарушений валидации
        $violation = new ConstraintViolation('Invalid value', null, [], '', 'email', 'invalid');
        $violations = new ConstraintViolationList([$violation]);
        $exception = new ValidationFailedException('data', $violations);

        $event = $this->createExceptionEvent($exception);

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode(['email' => 'Invalid value']), $response->getContent());
    }

    #[Test]
    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }
}
