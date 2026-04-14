<?php

namespace Unit\Controller\Web\Dashboard\Diary\GetDiaries;

use App\Controller\Web\Dashboard\Diary\GetDiaries\Controller;
use App\Controller\Web\Dashboard\Diary\GetDiaries\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class ControllerTest extends TestCase
{
    public function testInvokeReturnsResponseAndSetsSession(): void
    {
        // 1. Мокаем Manager
        $manager = $this->createMock(Manager::class);
        $manager->expects($this->once())
            ->method('getDiaries')
            ->with(2024, 3)
            ->willReturn([
                'diaryTitle' => 'Test Title',
                'diaryType' => 'Test Type',
                'tableHeader' => [],
                'tableBody' => []
            ]);

        // 2. Мокаем Request и Session
        $session = $this->createMock(SessionInterface::class);
        $request = new Request();
        $request->setSession($session);
        // Эмулируем URI
        $server = ['REQUEST_URI' => '/dashboard/diaries/2024/03'];
        $request->initialize([], [], [], [], [], $server);

        // Проверяем, что в сессию пишется путь
        $session->expects($this->once())
            ->method('set')
            ->with('_previous_route', '/dashboard/diaries/2024/03');

        // 3. Создаем контроллер
        $controller = new Controller($manager);

        // Чтобы работал $this->render(), нужно подменить контейнер (AbstractController его использует)
        $container = $this->createMock(ContainerInterface::class);
        $twig = $this->createMock(Environment::class);

        $container->method('has')->with('twig')->willReturn(true);
        $container->method('get')->with('twig')->willReturn($twig);

        $twig->method('render')->willReturn('html content');

        $controller->setContainer($container);

        // 4. Вызов
        $response = $controller->__invoke($request, 2024, 3);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('html content', $response->getContent());
    }
}
