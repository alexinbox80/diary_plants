<?php

namespace App\Infrastructure\EventSubscriber;

use Throwable;
use Twig\Environment;
use App\Domain\Service\IncidentService;
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
        private Environment $twig,
        private readonly bool $debug,
        private readonly IncidentService $incidentService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', 10]];
        //return [];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // 1. Определяем HTTP статус-код
        $statusCode = $this->mapExceptionToCode($exception);

        // 2. Если это 5xx ошибка на проде (не debug), логируем инцидент в БД
        $publicErrorCode = null;
        if ($statusCode >= 500 && $statusCode <= 599 && !$this->debug) {
            $publicErrorCode = $this->incidentService->createFromException($exception, $request, $statusCode);
        }

        // 3. Проверяем формат ответа (JSON или HTML)
        $isJsonRequest = $request->getContentTypeFormat() === 'json'
            || str_contains($request->getPathInfo(), '/api')
            || $request->headers->get('Accept') === 'application/json';

        if ($isJsonRequest) {
            $response = $this->createJsonResponse($exception, $statusCode, $publicErrorCode);
        } else {
            $response = $this->createHtmlResponse($exception, $statusCode, $publicErrorCode);
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

    private function createJsonResponse(Throwable $exception, int $statusCode, ?string $publicErrorCode): JsonResponse
    {
        if ($exception instanceof ValidationFailedException) {
            $errors = [];
            foreach ($exception->getViolations() as $violation) {
                $property = $violation->getPropertyPath() ?: self::DEFAULT_PROPERTY;
                $errors[$property] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], $statusCode);
        }

        // Защита информации на проде: скрываем системный текст ошибки для 5xx
        $message = $exception->getMessage();
        if ($statusCode >= 500 && $statusCode <= 599 && !$this->debug) {
            $message = 'Внутренняя ошибка сервера. Пожалуйста, передайте код ошибки в службу поддержки.';
        }

        $data = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => ($exception instanceof HttpCompliantExceptionInterface)
                ? $exception->getHttpResponseBody()
                : $message
        ];

        // Добавляем публичный код инцидента в ответ клиенту
        if ($publicErrorCode !== null) {
            $data['error_code'] = $publicErrorCode;
        }

        if ($this->debug) {
            $data['debug'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'class' => get_class($exception),
                'trace' => array_slice($exception->getTrace(), 0, 10),
            ];
        }

        return new JsonResponse($data, $statusCode);
    }

    private function createHtmlResponse(Throwable $exception, int $statusCode, ?string $publicErrorCode): Response
    {
        $template = sprintf('bundles/TwigBundle/Exception/error%s.html.twig', $statusCode);

        if (!$this->twig->getLoader()->exists($template)) {
            $template = 'bundles/TwigBundle/Exception/error.html.twig';
        }

        $message = $exception->getMessage();
        if ($statusCode >= 500 && $statusCode <= 599 && !$this->debug) {
            $message = 'На сервере произошел непредвиденный сбой. Мы уже работаем над его устранением.';
        }

        try {
            $content = $this->twig->render($template, [
                'status_code' => $statusCode,
                'message'     => $message,
                'exception'   => $exception,
                'error_code'  => $publicErrorCode,
            ]);
        } catch (Throwable) {
            $content = "<h1>Ошибка $statusCode</h1><p>{$message}</p>";
            if ($publicErrorCode) {
                $content .= "<p><b>Код инцидента:</b> {$publicErrorCode}</p>";
            }
        }

        return new Response($content, $statusCode);
    }
}
