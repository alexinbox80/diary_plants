<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\FileService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use App\Domain\Model\Attachment\CreateAttachmentModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly AttachmentService $attachmentService,
        private readonly FileService $fileService,
        private readonly string $csvImageFilePrefix
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'attachment';
    }

    public function import(array $data): void
    {
        $entityId = $data['attachable_id'];
        $entityType = $data['attachable_type'];

        // Формируем путь к папке с исходными картинками
        $sourceDir = rtrim($this->csvImageFilePrefix, '/') . '/' . $entityType . '/' . $entityId;

        // Если папки нет или она пуста — создаем запись без файла (или пропускаем)
        if (!is_dir($sourceDir) || empty($imageFiles = $this->fileService->getFilesInDirectory($sourceDir))) {
            $this->saveAttachment($data);
            return;
        }

        // Если файлы есть, обрабатываем каждый
        foreach ($imageFiles as $imagePath) {
            $mimeType = mime_content_type($imagePath);
            $fileName = basename($imagePath);

            // Эмулируем загрузку файла
            $uploadedFile = new UploadedFile(
                $imagePath,
                $fileName,
                $mimeType,
                null,
                true // test mode
            );

            // Определяем целевой путь в системе хранения (uploads/...)
            $targetPath = $this->fileService->getAttachmentsPath($entityType, $entityId);
            $storedFile = $this->fileService->storeUploadedFile($uploadedFile, $targetPath, false);

            // Обогащаем данные для модели
            $data['filename'] = $storedFile->getFilename();
            $data['path'] = $targetPath;
            $data['mime_type'] = $mimeType;

            $this->saveAttachment($data);
        }
    }

    private function saveAttachment(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateAttachmentModel::class, ...$this->map($data));
        $this->attachmentService->create($model);
    }

    private function map(array $d): array
    {
        return [
            (int) $d['group_id'],
            $this->str2bool($d['is_shown']),
            $this->es($d['filename']),
            $this->es($d['path']),
            $this->es($d['mime_type']),
            $d['alt'],
            $d['title'],
            $this->dt($d['file_date']),
            (int) $d['attachable_id'],
            $d['attachable_type'],
            $this->es($d['description']),
        ];
    }
}
