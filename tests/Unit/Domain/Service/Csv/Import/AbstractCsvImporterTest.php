<?php

namespace Unit\Domain\Service\Csv\Import;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\Service\Csv\Import\AbstractCsvImporter;

class AbstractCsvImporterTest extends TestCase
{
    private AbstractCsvImporter $importer;

    protected function setUp(): void
    {
        // Создаем анонимный класс-наследник для доступа к защищенным методам
        $this->importer = new class extends AbstractCsvImporter {
            public function testDt(?string $v): ?DateTimeImmutable { return $this->dt($v); }
            public function testPr(?string $v): ?Price { return $this->pr($v); }
            public function testStr2bool(mixed $v): bool { return $this->str2bool($v); }
            public function testEs(?string $v): ?string { return $this->es($v); }
        };
    }

    public function testDtConvertsValidDate(): void
    {
        $result = $this->importer->testDt('2024-03-02');
        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals('2024-03-02', $result->format('Y-m-d'));
    }

    public function testDtReturnsNullOnEmptyOrNull(): void
    {
        $this->assertNull($this->importer->testDt(''));
        $this->assertNull($this->importer->testDt(null));
    }

    public function testPrConvertsValidPrice(): void
    {
        // Предполагаем, что Price::fromString('100 RUR') работает корректно
        $result = $this->importer->testPr('100 RUR');
        $this->assertInstanceOf(Price::class, $result);
        $this->assertEquals('100 RUR', (string)$result);
    }

    public function testPrReturnsNullOnEmpty(): void
    {
        $this->assertNull($this->importer->testPr(''));
    }

    public function testStr2boolLogic(): void
    {
        $this->assertTrue($this->importer->testStr2bool('true'));
        $this->assertTrue($this->importer->testStr2bool('1'));
        $this->assertTrue($this->importer->testStr2bool('yes'));

        $this->assertFalse($this->importer->testStr2bool('false'));
        $this->assertFalse($this->importer->testStr2bool('0'));
        $this->assertFalse($this->importer->testStr2bool(''));
    }

    public function testEsConvertsEmptyToNull(): void
    {
        $this->assertNull($this->importer->testEs(''));
        $this->assertNull($this->importer->testEs(null));
        $this->assertEquals('Some text', $this->importer->testEs('Some text'));
    }
}
