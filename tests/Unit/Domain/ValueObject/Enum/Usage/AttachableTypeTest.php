<?php

namespace Unit\Domain\ValueObject\Enum\Usage;

use App\Domain\Entity\Pest;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Stimulant;
use App\Domain\Entity\Fertilizer;
use App\Domain\ValueObject\Enum\Usage\AttachableType;


class AttachableTypeTest extends TestCase
{
    public function testGetClassReturnsEntityNamespace(): void
    {
        $this->assertEquals(Fertilizer::class, AttachableType::getClass('fertilizer'));
        $this->assertEquals(Pest::class, AttachableType::getClass('pest'));
        $this->assertEquals(Stimulant::class, AttachableType::getClass('stimulant'));
        $this->assertEquals(Watering::class, AttachableType::getClass('watering'));
        $this->assertNull(AttachableType::getClass('non_existent'));
    }

    public function testFromClassMapsRegularClasses(): void
    {
        $this->assertSame(AttachableType::FERTILIZER, AttachableType::fromClass(Fertilizer::class));
        $this->assertSame(AttachableType::PEST, AttachableType::fromClass(Pest::class));
        $this->assertSame(AttachableType::STIMULANT, AttachableType::fromClass(Stimulant::class));
        $this->assertSame(AttachableType::WATERING, AttachableType::fromClass(Watering::class));
    }

    public function testFromClassHandlesDoctrineProxies(): void
    {
        // Имитируем автогенерируемый класс прокси Doctrine
        $proxyClass = 'Proxies\__CG__\App\Domain\Entity\Watering';
        $this->assertSame(AttachableType::WATERING, AttachableType::fromClass($proxyClass));
    }

    public function testFromClassThrowsExceptionForInvalidClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown class: stdClass');

        AttachableType::fromClass(\stdClass::class);
    }

    public function testIsValid(): void
    {
        $this->assertTrue(AttachableType::isValid('fertilizer'));
        $this->assertFalse(AttachableType::isValid('invalid_value'));
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Полив', AttachableType::getLabel('watering'));
        $this->assertEquals('Удобрение', AttachableType::getLabel('fertilizer'));
        $this->assertEquals('Неизвестно', AttachableType::getLabel('unknown'));
    }

    public function testAsSelectArray(): void
    {
        $expected = [
            'Удобрение' => 'fertilizer',
            'Вредитель' => 'pest',
            'Стимулятор' => 'stimulant',
            'Полив' => 'watering',
        ];

        $result = AttachableType::asSelectArray();

        $this->assertCount(4, $result);
        foreach ($expected as $label => $value) {
            $this->assertArrayHasKey($label, $result);
            $this->assertEquals($value, $result[$label]);
        }
    }

    public function testGetValues(): void
    {
        $expectedValues = ['fertilizer', 'pest', 'stimulant', 'watering'];
        $this->assertEquals($expectedValues, AttachableType::getValues());
    }
}
