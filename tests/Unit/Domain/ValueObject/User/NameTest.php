<?php

namespace Unit\Domain\ValueObject\User;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Name;

class NameTest extends TestCase
{
    public function testValidNameInitialization(): void
    {
        $last = 'Иванов';
        $first = 'Иван';
        $middle = 'Иванович';

        $name = new Name($last, $first, $middle);

        $this->assertEquals($first, $name->getFirst());
        $this->assertEquals($last, $name->getLast());
        $this->assertEquals($middle, $name->getMiddle());
        $this->assertEquals('Иван Иванов Иванович', $name->getFull());
    }

    public function testNameWithoutMiddle(): void
    {
        $name = new Name('Doe', 'John');

        $this->assertNull($name->getMiddle());
        $this->assertEquals('John Doe', $name->getFull());
        $this->assertEquals('John Doe', (string)$name);
    }

    public function testInvalidCharactersThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // Цифры не разрешены регулярным выражением
        new Name('Иванов', 'Иван123');
    }

    public function testTooShortNameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first name must be a string valid length of 2-64 letters');

        new Name('Иванов', 'И'); // 1 буква — слишком коротко
    }

    public function testIsEqualComparison(): void
    {
        $name1 = new Name('Иванов', 'Иван', 'Иванович');
        $name2 = new Name('Иванов', 'Иван', 'Иванович');
        $name3 = new Name('Иванов', 'Иван');

        $this->assertTrue($name1->isEqual($name2));
        $this->assertFalse($name1->isEqual($name3));
    }

    public function testEmptyStringsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('', 'Иван');
    }

    public function testLatinLettersSuccess(): void
    {
        $name = new Name('Smith', 'John');
        $this->assertEquals('John Smith', $name->getFull());
    }
}
