<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\FileService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use Symfony\Component\HttpFoundation\File\File;
use App\Domain\Service\Csv\Import\AttachmentImporter;
use App\Domain\Model\Attachment\CreateAttachmentModel;

class AttachmentImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private AttachmentService $attachmentService;
    private FileService $fileService;
    private string $prefix = '/tmp/test_images';
    private AttachmentImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->attachmentService = $this->createMock(AttachmentService::class);
        $this->fileService = $this->createMock(FileService::class);

        $this->importer = new AttachmentImporter(
            $this->modelFactory,
            $this->attachmentService,
            $this->fileService,
            $this->prefix
        );
    }

    public function testImportWithoutFiles(): void
    {
        $data = [
            'group_id' => '1',
            'is_shown' => '1',
            'filename' => null,
            'path' => null,
            'mime_type' => null,
            'alt' => 'No image',
            'title' => 'Title',
            'file_date' => '2024-01-01',
            'attachable_id' => '50',
            'attachable_type' => 'plant',
            'description' => ''
        ];

        // Мокаем отсутствие директории (библиотека FileService не вызывается для файлов)
        // В реальном коде is_dir проверит /tmp/test_images/plant/50
        // Чтобы не создавать папку, мы рассчитываем, что is_dir вернет false

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateAttachmentModel::class, 1, true, null, null, null, 'No image', 'Title', $this->isInstanceOf(\DateTimeImmutable::class), 50, 'plant', null)
            ->willReturn($this->createMock(CreateAttachmentModel::class));

        $this->attachmentService->expects($this->once())->method('create');

        $this->importer->import($data);
    }

    public function testImportWithFiles(): void
    {
        $data = [
            'group_id' => '1',
            'is_shown' => 'true',
            'alt' => 'Alt text',
            'title' => 'Title',
            'file_date' => '2024-01-01',
            'attachable_id' => '50',
            'attachable_type' => 'plant',
            'description' => 'Desc'
        ];

        // Создаем временный файл, чтобы mime_content_type и basename сработали
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'test_img_');
        file_put_contents($tmpFilePath, 'fake image content');

        // Мокаем поиск файлов
        $this->fileService->method('getFilesInDirectory')->willReturn([$tmpFilePath]);
        $this->fileService->method('getAttachmentsPath')->willReturn('uploads/plants/50');

        $storedFileMock = $this->createMock(File::class);
        $storedFileMock->method('getFilename')->willReturn('new_filename.jpg');

        $this->fileService->method('storeUploadedFile')->willReturn($storedFileMock);

        // Проверяем, что в модель попали данные из "хранилища"
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateAttachmentModel::class,
                1, true, 'new_filename.jpg', 'uploads/plants/50', 'text/plain', // mime для tempnam обычно text/plain
                'Alt text', 'Title', $this->anything(), 50, 'plant', 'Desc'
            )
            ->willReturn($this->createMock(CreateAttachmentModel::class));

        // Нужно создать директорию или замокать поведение is_dir.
        // В юнит тестах без vfsStream проще реально создать папку в temp:
        $dir = $this->prefix . '/plant/50';
        @mkdir($dir, 0777, true);

        $this->importer->import($data);

        // Чистим за собой
        unlink($tmpFilePath);
        rmdir($dir);
    }
}
