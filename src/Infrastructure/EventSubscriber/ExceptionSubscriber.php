<?php

namespace App\Infrastructure\EventSubscriber;

use Twig\Environment;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Exception\UserNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // Слушаем событие исключения
//        return [KernelEvents::EXCEPTION => ['onKernelException', 10]];
        return [];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Превращаем доменную ошибку в HTTP-код
        $code = $this->mapExceptionToCode($exception);

        // Выбираем формат (JSON или HTML) и отправляем ответ в $event->setResponse()
        $this->handleResponse($event, $code);
    }

    private function mapExceptionToCode(\Throwable $exception): int
    {
        return match (true) {
            // Доменные исключения (ваша бизнес-логика)
            $exception instanceof UserNotFoundException => 404,
            $exception instanceof AccessDeniedException => 403,

            // Ошибки самого Symfony (например, из контроллеров)
            $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),

            // Все остальное — это критическая ошибка сервера
            default => 500,
        };
    }

    private function handleResponse(ExceptionEvent $event, int $statusCode): void
    {
        $request = $event->getRequest();
        $exception = $event->getThrowable();

        // 1. Проверяем, что хочет клиент: JSON или HTML?
        if ($request->getContentTypeFormat() === 'json' || str_contains($request->getPathInfo(), '/api')) {

            $response = new JsonResponse([
                'status' => 'error',
                'code'   => $statusCode,
                'message' => $exception->getMessage(), // В проде лучше скрывать детали для 500 ошибки
            ], $statusCode);

        } else {

            // 2. Иначе готовим HTML через Twig
            // Пытаемся найти шаблон под конкретный код (error404.html.twig)
            $template = sprintf('bundles/TwigBundle/Exception/error%s.html.twig', $statusCode);

            if (!$this->twig->getLoader()->exists($template)) {
                $template = 'bundles/TwigBundle/Exception/error.html.twig';
            }

            $content = $this->twig->render($template, [
                'status_code' => $statusCode,
                'exception'   => $exception,
            ]);

            $response = new Response($content, $statusCode);
        }

        // 3. Передаем готовый ответ в событие.
        // После этого Symfony прекратит поиск других обработчиков и отправит этот Response пользователю.
        $event->setResponse($response);
    }
}
