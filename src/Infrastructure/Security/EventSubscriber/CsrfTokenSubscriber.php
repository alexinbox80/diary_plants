<?php

namespace App\Infrastructure\Security\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CsrfTokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Слушаем событие перед вызовом контроллера
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        // 1. Проверяем только API запросы (например, начинающиеся с /api/v1/dashboard)
        if (!str_starts_with($request->getPathInfo(), '/api/v1/dashboard')) {
            return;
        }

        // 2. Проверяем только "опасные" методы (POST, DELETE, PUT)
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
            return;
        }

        // 3. Извлекаем токен из заголовка (который шлет ваш dataHandler.js)
        $tokenValue = $request->headers->get('X-CSRF-TOKEN');

        // 4. Валидация. 'authenticate' — тот же ID, что в Twig и конфиге yaml
        $token = new CsrfToken('authenticate', $tokenValue);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            // Если токен невалиден, выбрасываем исключение 403
            throw new AccessDeniedHttpException('Invalid or missing CSRF token.');
        }
    }
}
