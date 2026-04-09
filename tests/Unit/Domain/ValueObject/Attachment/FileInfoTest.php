<?php

namespace Unit\Domain\ValueObject\Attachment;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Attachment\FileInfo;

class FileInfoTest extends TestCase
{
    public function testInitializationWithFullData(): void
    {
        $filename = 'photo.jpg';
        $path = '/uploads/2023/photo.jpg';
        $mimeType = 'image/jpeg';
        $date = new DateTimeImmutable('2023-10-27 12:00:00');

        $fileInfo = new FileInfo($filename, $path, $mimeType, $date);

        $this->assertEquals($filename, $fileInfo->getFilename());
        $this->assertEquals($path, $fileInfo->getPath());
        $this->assertEquals($mimeType, $fileInfo->getMimeType());
        $this->assertSame($date, $fileInfo->getFileDate());
    }

    public function testEmptyInitialization(): void
    {
        $fileInfo = new FileInfo();

        $this->assertNull($fileInfo->getFilename());
        $this->assertNull($fileInfo->getPath());
        $this->assertNull($fileInfo->getMimeType());
        $this->assertNull($fileInfo->getFileDate());
    }

    public function testDateImmutabilityInGetters(): void
    {
        $date = new DateTimeImmutable();
        $fileInfo = new FileInfo(fileDate: $date);

        // Проверяем, что возвращается тот же объект даты (или идентичный по значению)
        $this->assertEquals($date, $fileInfo->getFileDate());
        $this->assertInstanceOf(DateTimeImmutable::class, $fileInfo->getFileDate());
    }

    public function testPartialData(): void
    {
        $fileInfo = new FileInfo(filename: 'document.pdf');

        $this->assertEquals('document.pdf', $fileInfo->getFilename());
        $this->assertNull($fileInfo->getPath());
    }
}
