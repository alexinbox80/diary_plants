<?php

namespace App\Tests\Integration\Infrastructure\Repository;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Repository\MarkerRepository;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MarkerRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private MarkerRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository = self::getContainer()->get(MarkerRepository::class);

        // Очистка таблиц через Native SQL с использованием кавычек для PostgreSQL
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('TRUNCATE marker RESTART IDENTITY CASCADE');
        $connection->executeStatement('TRUNCATE "group" RESTART IDENTITY CASCADE');
    }

    private function createGroup(string $title): Group
    {
        $group = new Group(true, $title);
        $this->entityManager->persist($group);
        return $group;
    }

    public function testGetMarkersForFormFiltersCorrectly(): void
    {
        // 1. Подготовка данных
        $groupA = $this->createGroup('Группа А');
        $groupB = $this->createGroup('Группа Б');

        // Создаем маркеры с разным типом и группами
        $marker1 = new Marker($groupA, 'FE', '#00FF00', AttachableType::FERTILIZER);
        $marker2 = new Marker($groupA, 'PE', '#FF0000', AttachableType::PEST);
        $marker3 = new Marker($groupB, 'FE', '#0000FF', AttachableType::FERTILIZER);

        $this->entityManager->persist($marker1);
        $this->entityManager->persist($marker2);
        $this->entityManager->persist($marker3);
        $this->entityManager->flush();

        // 2. Тестируем фильтрацию: только FERTILIZER для Группы А
        $results = $this->repository->getMarkersForForm($groupA->getId(), AttachableType::FERTILIZER->value);

        // 3. Проверки
        $this->assertCount(1, $results);
        $this->assertEquals('FE', $results[0]->getLetter());
        $this->assertEquals($groupA->getId(), $results[0]->getGroup()->getId());
    }

    public function testFindReturnsMarkerWithJoinedGroup(): void
    {
        $group = $this->createGroup('Проверка Join');
        $marker = new Marker($group, 'TST', '#123456', AttachableType::WATERING);

        $this->repository->create($marker);
        $this->entityManager->clear(); // Очищаем кеш, чтобы Doctrine сделала реальный SQL запрос

        // Вызываем find (который использует getBaseQueryBuilder с leftJoin)
        $foundMarker = $this->repository->find($marker->getId());

        $this->assertNotNull($foundMarker);
        $this->assertEquals('TST', $foundMarker->getLetter());
        // Проверяем, что группа доступна без дополнительных запросов
        $this->assertEquals('Проверка Join', $foundMarker->getGroup()->getTitle());
    }

    public function testRemoveMarksAsDeleted(): void
    {
        $group = $this->createGroup('Удаление');
        $marker = new Marker($group, 'DEL', '#654321', AttachableType::STIMULANT);
        $this->repository->create($marker);

        // Вызываем soft delete
        $this->repository->remove($marker);

        // Проверяем, что дата удаления установилась
        $this->assertNotNull($marker->getDeletedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $marker->getDeletedAt());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
