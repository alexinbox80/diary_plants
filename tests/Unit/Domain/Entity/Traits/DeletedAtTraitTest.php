<?php

namespace Unit\Domain\Entity\Traits;

use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\ValueObject\Plant\PlantIdentifier;

class DeletedAtTraitTest extends TestCase
{
    public function testBasicSoftDelete(): void
    {
        $object = new class { use DeletedAtTrait; };
        $object->setDeletedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $object->getDeletedAt());
    }

    public function testPlantSoftDeleteLogic(): void
    {
        // Создаем анонимный класс
        $plantMock = new class extends Plant {
            use DeletedAtTrait;
            // Объявляем свойство здесь, чтобы избежать Deprecation "dynamic property"
            protected PlantIdentifier $plantIdentifier;

            public function __construct() {}

            // Переопределяем методы, чтобы они работали с нашим локальным свойством
            public function changePlantIdentifier(PlantIdentifier $plantIdentifier): self
            {
                $this->plantIdentifier = $plantIdentifier;
                return $this;
            }
            public function getPlantIdentifier(): PlantIdentifier
            {
                return $this->plantIdentifier;
            }
        };

        $oid = OId::next();
        $originalIdentifier = new PlantIdentifier($oid);
        $plantMock->changePlantIdentifier($originalIdentifier);

        // Запускаем удаление
        $plantMock->setDeletedAt();

        // 1. Проверяем дату
        $this->assertNotNull($plantMock->getDeletedAt());

        // 2. Проверяем, что объект заменен
        $this->assertNotSame(
            $originalIdentifier,
            $plantMock->getPlantIdentifier(),
            'Объект должен был замениться новым экземпляром'
        );

        // 3. Проверяем сохранение OId
        $this->assertTrue(
            $plantMock->getPlantIdentifier()->getOid()->isEqual($oid)
        );
    }
}
