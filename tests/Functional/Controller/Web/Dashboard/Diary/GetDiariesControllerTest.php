<?php

namespace Functional\Controller\Web\Dashboard\Diary;

use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use App\Domain\ValueObject\User\Name;
use App\Domain\ValueObject\User\Email;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GetDiariesControllerTest extends WebTestCase
{
    public function testIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        // 1. Подготовка Group (первый аргумент bool $isActive, второй string $title)
        $groupRepository = $em->getRepository(Group::class);
        $group = $groupRepository->findOneBy([]) ?? new Group(
            true,
            'Тестовая группа',
            'Описание тестовой группы'
        );

        if (!$em->contains($group)) {
            $em->persist($group);
            $em->flush(); // Сначала сохраняем группу, чтобы получить ID
        }

        // 2. Подготовка User
        $emailStr = 'test@example.com';
        $userRepository = $em->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => $emailStr]);

        if (!$testUser) {
            $testUser = new User(
                $group,
                new Email($emailStr),
                'temporary_hash', // Будет обновлен через upgradePassword
                new Name('Ivan', 'Ivanov', 'Ivanovich'),
                ['ROLE_USER']
            );

            // Хешируем пароль
            $hasher = $container->get(UserPasswordHasherInterface::class);
            $testUser->upgradePassword($hasher->hashPassword($testUser, 'password123'));

            $testUser->activate();

            $em->persist($testUser);
            $em->flush();
        }

        // 3. Авторизация
        $client->loginUser($testUser);

        // 4. Выполнение запроса
        $client->request('GET', '/ru/dashboard/diaries/2024/03');

        // 5. Проверки (Ассерты)
        $this->assertResponseIsSuccessful();

        // Проверяем сессию (если ваш контроллер пишет в нее _previous_route)
        $session = $client->getRequest()->getSession();
        if ($session->has('_previous_route')) {
            $this->assertEquals('/ru/dashboard/diaries/2024/03', $session->get('_previous_route'));
        }

        // Проверка наличия заголовка в DOM
        $this->assertSelectorTextContains('h5', 'Дневник растений');
    }
}
