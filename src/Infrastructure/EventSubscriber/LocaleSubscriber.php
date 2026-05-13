<?php

namespace App\Infrastructure\EventSubscriber;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private const string COOKIE_NAME = '_locale';
    private const string SET_COOKIE_ATTR = '_set_locale_cookie';

    public function __construct(
        private readonly string $defaultLocale = 'ru',
        private readonly array $supportedLocales = ['ru', 'en']
    ) {
    }

    /**
     * Шаг 1: Определяем локаль при входящем запросе
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo(); // Например, "/dashboard/plants-paginated" или "/ru/dashboard/..."

        // 1. Игнорируем API, профайлер и системные запросы ассетов
        if (str_starts_with($path, '/api') || str_starts_with($path, '/_')) {
            return;
        }

        // 2. ЗАЩИТА ОТ ЦИКЛА: Проверяем, начинается ли путь СРАЗУ с валидной локали
        // Регулярное выражение ищет "/ru/" или "/en/" (или "/ru", "/en" в конце строки)
        if (preg_match('#^/(ru|en)(/|$)#', $path)) {
            // Локаль уже есть в URL! Вытаскиваем её и задаем системе
            $urlLocale = explode('/', trim($path, '/'))[0];
            $request->setLocale($urlLocale);
            return;
        }

        // 3. Если мы дошли сюда, значит в URL ТОЧНО нет локали (например, "/dashboard/plants-paginated")
        // Определяем целевой язык (кука -> браузер -> дефолт)
        $targetLocale = $this->defaultLocale;
        if ($request->cookies->has('_locale')) {
            $cookieLocale = $request->cookies->get('_locale');
            if (in_array($cookieLocale, $this->supportedLocales, true)) {
                $targetLocale = $cookieLocale;
            }
        } else {
            $browserLocale = $request->getPreferredLanguage($this->supportedLocales);
            if ($browserLocale) {
                $targetLocale = $browserLocale;
            }
        }

        // 4. Формируем чистый путь для редиректа
        $newPath = '/' . $targetLocale . $path;

        $queryString = $request->getQueryString();
        if ($queryString) {
            $newPath .= '?' . $queryString;
        }

        // Выполняем безопасный редирект
        $response = new RedirectResponse($newPath);

        // Пишем куку, если её не было
        if (!$request->cookies->has(self::COOKIE_NAME)) {
            $cookie = Cookie::create(self::COOKIE_NAME)
                ->withValue($targetLocale)
                ->withExpires(new DateTimeImmutable('+1 year'))
                ->withPath('/')
                ->withSecure(false)
                ->withHttpOnly(true);
            $response->headers->setCookie($cookie);
        }

        $event->setResponse($response);
    }

    /**
     * Шаг 2: Записываем куку в исходящий HTTP-ответ, если сработал флаг
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        // Если флага нет (язык уже был в куке или это API) — ничего не делаем
        if (!$request->attributes->has(self::SET_COOKIE_ATTR)) {
            return;
        }

        $localeToSave = $request->attributes->get(self::SET_COOKIE_ATTR);
        $response = $event->getResponse();

        // Создаем куку на 1 год
        $cookie = Cookie::create(self::COOKIE_NAME)
            ->withValue($localeToSave)
            ->withExpires(new DateTimeImmutable('+1 year'))
            ->withPath('/')
            ->withSecure(false)   // Защищенный режим (для HTTPS)
            ->withHttpOnly(true); // Защита от XSS (JS не сможет прочитать куку)

        $response->headers->setCookie($cookie);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
