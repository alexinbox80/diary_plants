<?php

namespace Unit\Domain\Service;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\FileService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\File\File;
use App\Infrastructure\Storage\LocalFileStorage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[CoversClass(FileService::class)]
class FileServiceTest extends TestCase
{
    private LocalFileStorage|MockObject $storage;
    private FileService $service;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(LocalFileStorage::class);
        $this->service = new FileService($this->storage);
    }

    #[Test]
    public function testGetAttachmentsPath(): void
    {
        $type = 'Plant::Entity';
        $id = 42;

        $path = $this->service->getAttachmentsPath($type, $id);

        // Проверяем логику explode и сборки пути
        $this->assertSame('attachments/Plant/42/', $path);
    }

    #[Test]
    public function testStoreUploadedFile(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $directory = 'target/dir';
        $expectedFile = $this->createMock(File::class);

        $this->storage->expects($this->once())
            ->method('storeUploadedFile')
            ->with($uploadedFile, $directory, true)
            ->willReturn($expectedFile);

        $result = $this->service->storeUploadedFile($uploadedFile, $directory);

        $this->assertSame($expectedFile, $result);
    }

    #[Test]
    public function testGetFilesInDirectory(): void
    {
        $dir = 'some/path';
        $files = ['file1.jpg', 'file2.png'];

        $this->storage->expects($this->once())
            ->method('getFilesInDirectory')
            ->with($dir)
            ->willReturn($files);

        $result = $this->service->getFilesInDirectory($dir);

        $this->assertSame($files, $result);
    }

    #[Test]
    public function testRemoveUploadedFile(): void
    {
        $filePath = 'path/to/file.pdf';

        $this->storage->expects($this->once())
            ->method('removeUploadedFile')
            ->with($filePath)
            ->willReturn(true);

        $result = $this->service->removeUploadedFile($filePath);

        $this->assertTrue($result);
    }

    #[Test]
    public function testGetQrCodeLink(): void
    {
        $dir = 'qrcodes';
        $uuid = '123-uuid';
        $url = 'https://example.com';
        $expectedPath = 'qrcodes/123-uuid.png';

        $this->storage->expects($this->once())
            ->method('createQrCodeFile')
            ->with($dir, $uuid, $url)
            ->willReturn($expectedPath);

        $result = $this->service->getQrCodeLink($dir, $uuid, $url);

        $this->assertSame($expectedPath, $result);
    }
}
