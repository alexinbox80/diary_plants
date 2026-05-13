<?php

namespace App\Controller\Web;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class LocaleController extends AbstractController
{
    #[Route('/switch-language/{locale}', name: 'app.switch.language', requirements: ['locale' => 'ru|en'])]
    public function switch(string $locale, Request $request): Response
    {
        // Берем URL страницы, с которой пришел пользователь (или на дашборд)
        $referer = $request->headers->get('referer');

        // Если реферер содержит старую локаль в URL (например, /ru/dashboard),
        // меняем её в строке на новую
        if ($referer) {
            $referer = preg_replace('/(\/(ru|en)\/)/', '/' . $locale . '/', $referer, 1);
            $response = $this->redirect($referer);
        } else {
            $response = $this->redirectToRoute('dashboard.index', ['_locale' => $locale]);
        }

        // Создаем куку со сроком действия 1 год
        $cookie = Cookie::create('_locale')
            ->withValue($locale)
            ->withExpires(new DateTimeImmutable('+1 year'))
            ->withPath('/')
            ->withSecure(false) // true, если используется https
            ->withHttpOnly(true);

        $response->headers->setCookie($cookie);

        return $response;
    }
}
