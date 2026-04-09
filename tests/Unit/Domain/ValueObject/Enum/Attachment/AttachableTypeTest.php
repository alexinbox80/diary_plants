<?php

namespace Unit\Domain\ValueObject\Enum\Attachment;

use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

class AttachableTypeTest extends TestCase
{
    public function testGetClassReturnsCorrectClassName(): void
    {
        $this->assertEquals(Plant::class, AttachableType::getClass('plant'));
        $this->assertEquals(Offspring::class, AttachableType::getClass('offspring'));
        $this->assertNull(AttachableType::getClass('unknown'));
    }

    public function testFromClassWithRegularClasses(): void
    {
        $this->assertSame(AttachableType::PLANT, AttachableType::fromClass(Plant::class));
        $this->assertSame(AttachableType::OFFSPRING, AttachableType::fromClass(Offspring::class));
    }

    public function testFromClassWithDoctrineProxies(): void
    {
        $proxyClassName = 'Proxies\__CG__\App\Domain\Entity\Plant';
        $this->assertSame(AttachableType::PLANT, AttachableType::fromClass($proxyClassName));
    }

    public function testFromClassThrowsExceptionOnUnknownClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown class: stdClass');

        AttachableType::fromClass(\stdClass::class);
    }

    public function testIsValid(): void
    {
        $this->assertTrue(AttachableType::isValid('plant'));
        $this->assertTrue(AttachableType::isValid('offspring'));
        $this->assertFalse(AttachableType::isValid('invalid_type'));
    }

    public function testGetChoices(): void
    {
        $expected = [
            'Растение' => 'plant',
            'Плод' => 'offspring',
        ];
        $this->assertEquals($expected, AttachableType::getChoices());
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Растение', AttachableType::getLabel('plant'));
        $this->assertEquals('Плод', AttachableType::getLabel('offspring'));
        $this->assertEquals('Неизвестно', AttachableType::getLabel('something_else'));
    }
}
