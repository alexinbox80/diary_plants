<?php

namespace Unit\Application\Doctrine;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Application\Doctrine\SoftDeletedFilter;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;

#[CoversClass(SoftDeletedFilter::class)]
class SoftDeletedFilterTest extends TestCase
{
    private SoftDeletedFilter $filter;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $this->filter = new SoftDeletedFilter($em);
    }

    #[Test]
    public function testAddFilterConstraintWhenEntityIsSoftDeletable(): void
    {
        // Мокаем ClassMetadata
        $targetEntity = $this->createMock(ClassMetadata::class);
        $reflClass = $this->createMock(ReflectionClass::class);

        // Настраиваем: сущность РЕАЛИЗУЕТ интерфейс
        $reflClass->method('implementsInterface')
            ->with(SoftDeletableInterface::class)
            ->willReturn(true);

        $targetEntity->reflClass = $reflClass;

        $result = $this->filter->addFilterConstraint($targetEntity, 't');

        $this->assertEquals('t.deleted_at IS NULL', $result);
    }

    #[Test]
    public function testAddFilterConstraintWhenEntityIsNotSoftDeletable(): void
    {
        $targetEntity = $this->createMock(ClassMetadata::class);
        $reflClass = $this->createMock(ReflectionClass::class);

        // Настраиваем: сущность НЕ реализует интерфейс
        $reflClass->method('implementsInterface')
            ->with(SoftDeletableInterface::class)
            ->willReturn(false);

        $targetEntity->reflClass = $reflClass;

        $result = $this->filter->addFilterConstraint($targetEntity, 't');

        $this->assertEquals('', $result);
    }
}
