<?php

namespace Functional\Controller\Web\Dashboard\Diary;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetDiariesControllerTest extends WebTestCase
{
    public function testIndexPageIsSuccessful(): void
    {
        $client = static::createClient();

        // Эмулируем авторизацию, если доступ закрыт (опционально)
        // $client->loginUser($testUser);

        // 1. Делаем запрос
        $client->request('GET', '/dashboard/diaries/2024/03');

        // 2. Проверяем статус ответа
        $this->assertResponseIsSuccessful();

        // 3. Проверяем, что в сессии сохранился предыдущий маршрут
        $session = $client->getRequest()->getSession();
        $this->assertEquals('/dashboard/diaries/2024/03', $session->get('_previous_route'));

        // 4. Проверяем наличие текста из шаблона (например, заголовок таблицы)
        $this->assertSelectorTextContains('h5', 'Дневник растений');
    }
}
