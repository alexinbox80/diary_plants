<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Plant\LifeCycle;
use App\Domain\ValueObject\Plant\SalesInfo;
use Doctrine\Common\Collections\Collection;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Domain\ValueObject\Plant\PlantIdentifier;

class PlantTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $title = 'Monstera';
        $room = 'Living Room';

        $plant = new Plant($group, $title, $room);

        $this->assertSame($group, $plant->getGroup());
        $this->assertEquals($title, $plant->getTitle());
        $this->assertEquals($room, $plant->getRoom());

        // Проверка инициализации VO
        $this->assertInstanceOf(PlantIdentifier::class, $plant->getPlantIdentifier());
        $this->assertInstanceOf(LifeCycle::class, $plant->getLifeCycle());
        $this->assertInstanceOf(PurchaseInfo::class, $plant->getPurchaseInfo());
        $this->assertInstanceOf(SalesInfo::class, $plant->getSalesInfo());

        // Проверка коллекций
        $this->assertInstanceOf(Collection::class, $plant->getRepottings());
        $this->assertInstanceOf(Collection::class, $plant->getUsages());
        $this->assertInstanceOf(Collection::class, $plant->getOffsprings());
    }

    #[DataProvider('invalidTitleProvider')]
    public function testTitleValidationFails(string $invalidTitle): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Plant($this->createGroupMock(), $invalidTitle, 'Kitchen');
    }

    public static function invalidTitleProvider(): array
    {
        return [
            'empty string' => [''],
            'single letter' => ['A'],
            'too long string' => [str_repeat('s', 256)],
        ];
    }

    public function testVisibilityToggle(): void
    {
        $plant = new Plant($this->createGroupMock(), 'Ficus', 'Hall');

        $this->assertTrue($plant->isShown());
        $plant->hide();
        $this->assertFalse($plant->isShown());
        $plant->show();
        $this->assertTrue($plant->isShown());
    }

    public function testChangeValueObjects(): void
    {
        $plant = new Plant($this->createGroupMock(), 'Cactus', 'Office');

        $newLifeCycle = $this->createMock(LifeCycle::class);
        $plant->changeLifeCycle($newLifeCycle);
        $this->assertSame($newLifeCycle, $plant->getLifeCycle());

        $newSalesInfo = $this->createMock(SalesInfo::class);
        $plant->changeSalesInfo($newSalesInfo);
        $this->assertSame($newSalesInfo, $plant->getSalesInfo());
    }
}
