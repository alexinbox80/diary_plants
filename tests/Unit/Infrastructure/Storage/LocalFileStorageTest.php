<?php

namespace App\Tests\Unit\Infrastructure\Storage;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Endroid\QrCode\Builder\BuilderInterface;
use App\Infrastructure\Storage\LocalFileStorage;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalFileStorageTest extends TestCase
{
    private $root;
    private $storage;
    private $qrBuilderMock;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('uploads');
        $this->qrBuilderMock = $this->createMock(BuilderInterface::class);

        $this->storage = new LocalFileStorage(
            $this->root->url(),
            $this->qrBuilderMock
        );
    }

    #[Test]
    public function getFilesInDirectory(): void
    {
        vfsStream::newFile('file1.txt')->at($this->root);
        vfsStream::newDirectory('subdir')->at($this->root);

        $files = $this->storage->getFilesInDirectory($this->root->url());

        $this->assertCount(1, $files);
        $this->assertStringContainsString('file1.txt', $files[0]);
    }

    #[Test]
    public function storeUploadedFileWithCopy(): void
    {
        // Создаем файл в vfsStream
        $sourcePath = $this->root->url() . '/source.jpg';
        file_put_contents($sourcePath, 'fake content');

        // Важно: UploadedFile в тестовом режиме (5-й параметр true)
        // Для vfsStream путь $uploadedFile->getRealPath() может быть пустым,
        // поэтому используем конструктор с корректным путем.
        $uploadedFile = new UploadedFile(
            $sourcePath,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        // В LocalFileStorage.php метод copy() падал из-за пустого пути.
        // Если getRealPath() в vfsStream не работает, можно замокать UploadedFile
        // или использовать прямое обращение к пути.
        $file = $this->storage->storeUploadedFile($uploadedFile, 'avatars', false);

        $this->assertFileExists($file->getPathname());
        $this->assertEquals('fake content', file_get_contents($file->getPathname()));
    }

    #[Test]
    public function createQrCodeFile(): void
    {
        $uuid = 'test-uuid';
        $url = 'https://google.com';

        // ИСПРАВЛЕНИЕ: Мокаем ResultInterface, так как PngResult помечен как final
        $qrResultMock = $this->createMock(ResultInterface::class);
        $qrResultMock->expects($this->once())
            ->method('saveToFile')
            ->with($this->stringContains($uuid . '.png'));

        $this->qrBuilderMock->method('build')->willReturn($qrResultMock);

        $path = $this->storage->createQrCodeFile('qrcodes/', $uuid, $url);

        $this->assertEquals('qrcodes/' . $uuid . '.png', $path);
    }
}
