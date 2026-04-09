<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Doctrine\Common\Collections\Collection;

class GroupTest extends TestCase
{
    /**
     * Проверяем корректную работу конструктора и геттеров
     */
    public function testConstructorAndState(): void
    {
        $group = new Group(
            isActive: true,
            title: 'Суккуленты',
            description: 'Растения пустынь'
        );

        $this->assertEquals('Суккуленты', $group->getTitle());
        $this->assertEquals('Растения пустынь', $group->getDescription());
        $this->assertTrue($group->isActive());
    }

    /**
     * Проверяем, что getId() выбрасывает исключение для новой сущности
     */
    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $group = new Group(true, 'Test');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Group is null.');

        $group->getId();
    }

    /**
     * Проверяем, что все 11 коллекций инициализированы и пусты
     */
    #[DataProvider('collectionGetterProvider')]
    public function testCollectionsInitialization(string $getter): void
    {
        $group = new Group(true, 'Test');

        $this->assertTrue(method_exists($group, $getter), "Метод $getter должен существовать");

        $collection = $group->$getter();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertCount(0, $collection);
    }

    public static function collectionGetterProvider(): array
    {
        return [
            ['getPlants'],
            ['getRepottings'],
            ['getWaterings'],
            ['getFertilizers'],
            ['getUsages'],
            ['getAttachments'],
            ['getOffsprings'],
            ['getStimulants'],
            ['getPests'],
            ['getUsers'],
            ['getMarkers'],
        ];
    }

    /**
     * Проверяем метод обновления полей
     */
    public function testChangeFields(): void
    {
        $group = new Group(true, 'Old Title');

        $group->changeFields(
            isActive: false,
            title: 'New Title',
            description: 'New Desc'
        );

        $this->assertFalse($group->isActive());
        $this->assertEquals('New Title', $group->getTitle());
        $this->assertEquals('New Desc', $group->getDescription());
    }
}
