<?php

namespace App\Infrastructure\EventSubscriber;

use Throwable;
use Twig\Environment;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Controller\Exception\HttpCompliantExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException as SecurityAccessDeniedException;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    private const DEFAULT_PROPERTY = 'error';

    public function __construct(
        private Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', 10]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // 1. Определяем HTTP статус-код
        $statusCode = $this->mapExceptionToCode($exception);

        // 2. Проверяем, нужно ли отдавать JSON (для API или заголовка Accept: application/json)
        $isJsonRequest = $request->getContentTypeFormat() === 'json'
            || str_contains($request->getPathInfo(), '/api')
            || $request->headers->get('Accept') === 'application/json';

        if ($isJsonRequest) {
            $response = $this->createJsonResponse($exception, $statusCode);
        } else {
            $response = $this->createHtmlResponse($exception, $statusCode);
        }

        $event->setResponse($response);
    }

    private function mapExceptionToCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof HttpCompliantExceptionInterface => $exception->getHttpCode(),
            $exception instanceof ValidationFailedException => Response::HTTP_BAD_REQUEST,
            $exception instanceof UserNotFoundException => Response::HTTP_NOT_FOUND,
            $exception instanceof AccessDeniedException,
            $exception instanceof SecurityAccessDeniedException => Response::HTTP_FORBIDDEN,
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }

    private function createJsonResponse(Throwable $exception, int $statusCode): JsonResponse
    {
        // Обработка ошибок валидации (из Listener)
        if ($exception instanceof ValidationFailedException) {
            $errors = [];
            foreach ($exception->getViolations() as $violation) {
                $property = $violation->getPropertyPath() ?: self::DEFAULT_PROPERTY;
                $errors[$property] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], $statusCode);
        }

        // Данные из кастомного интерфейса или обычное сообщение
        $message = ($exception instanceof HttpCompliantExceptionInterface)
            ? $exception->getHttpResponseBody()
            : $exception->getMessage();

        return new JsonResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message
        ], $statusCode);
    }

    private function createHtmlResponse(Throwable $exception, int $statusCode): Response
    {
        $template = sprintf('bundles/TwigBundle/Exception/error%s.html.twig', $statusCode);

        if (!$this->twig->getLoader()->exists($template)) {
            $template = 'bundles/TwigBundle/Exception/error.html.twig';
        }

        try {
            $content = $this->twig->render($template, [
                'status_code' => $statusCode,
                'exception'   => $exception,
            ]);
        } catch (Throwable) {
            // Если Twig сломался, отдаем простой текст, чтобы не было бесконечного цикла 500 ошибки
            $content = '<h1>Error ' . $statusCode . '</h1><p>' . $exception->getMessage() . '</p>';
        }

        return new Response($content, $statusCode);
    }
}
