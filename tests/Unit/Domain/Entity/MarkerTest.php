<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

class MarkerTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $letter = 'NPK';
        $color = '#FF5733';
        $type = AttachableType::FERTILIZER;
        $desc = 'Удобрение для роста';
        $colorDesc = 'Яркий оранжевый';

        $marker = new Marker($group, $letter, $color, $type, $desc, $colorDesc);

        $this->assertSame($group, $marker->getGroup());
        $this->assertEquals($letter, $marker->getLetter());
        $this->assertEquals($color, $marker->getColor());
        $this->assertSame($type, $marker->getType());
        $this->assertEquals($desc, $marker->getDescription());
        $this->assertEquals($colorDesc, $marker->getColorDescription());

        // Проверка инициализации коллекций
        $this->assertInstanceOf(Collection::class, $marker->getFertilizers());
        $this->assertInstanceOf(Collection::class, $marker->getPests());
        $this->assertInstanceOf(Collection::class, $marker->getWaterings());
        $this->assertInstanceOf(Collection::class, $marker->getStimulants());
    }

    #[DataProvider('validColorProvider')]
    public function testValidColorFormat(string $validColor): void
    {
        $marker = new Marker($this->createGroupMock(), 'M', $validColor, AttachableType::WATERING);
        $this->assertEquals($validColor, $marker->getColor());
    }

    public static function validColorProvider(): array
    {
        return [
            ['#FFFFFF'],
            ['#000000'],
            ['#a1B2c3'],
        ];
    }

    /**
     * Тестируем невалидные цвета.
     * Ожидаем либо ошибку длины, либо ошибку формата регулярного выражения.
     */
    #[DataProvider('invalidColorProvider')]
    public function testInvalidColorFormat(string $invalidColor, string $expectedMessagePart): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessagePart);

        new Marker($this->createGroupMock(), 'M', $invalidColor, AttachableType::WATERING);
    }

    public static function invalidColorProvider(): array
    {
        return [
            'No hash' => ['FFFFFF', 'Color must be a string valid length of 7 letters'],
            'Too short' => ['#FFF', 'Color must be a string valid length of 7 letters'],
            'Invalid HEX' => ['#GGGGGG', 'Invalid color format'],
            'Too long' => ['#1234567', 'Color must be a string valid length of 7 letters'],
        ];
    }

    public function testLetterValidationFails(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Title must be a string valid length of 1-3 letters');

        new Marker($this->createGroupMock(), 'LONG', '#000000', AttachableType::STIMULANT);
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $marker = new Marker($this->createGroupMock(), 'ID', '#000000', AttachableType::WATERING);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Marker is null.');

        $marker->getId();
    }
}
