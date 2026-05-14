<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Attachment;
use App\Domain\Service\FileService;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\AttachmentService;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Model\Attachment\CreateAttachmentModel;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\ValueObject\Attachment\DisplaySettings;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Domain\Repository\AttachmentRepositoryInterface;
use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;
use App\Controller\Web\Dashboard\Image\CreateImage\Input\CreateImageDTO;

class AttachmentServiceTest extends TestCase
{
    private AttachmentRepositoryInterface&MockObject $repository;
    private ModelFactory&MockObject $modelFactory;
    private FileService&MockObject $fileService;
    private GroupService&MockObject $groupService;
    private AttachmentService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AttachmentRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->fileService = $this->createMock(FileService::class);
        $this->groupService = $this->createMock(GroupService::class);

        $this->service = new AttachmentService(
            $this->repository,
            $this->modelFactory,
            $this->fileService,
            $this->groupService
        );
    }

    public function testCreateSavesAttachmentAndReturnsModel(): void
    {
        // Создаем реальную модель, так как свойства readonly
        $model = new CreateAttachmentModel(
            groupId: 10,
            isShown: true,
            filename: 'image.jpg',
            path: '/uploads/',
            mimeType: 'image/jpeg',
            alt: 'Alt text',
            title: 'Test Title',
            fileDate: new DateTimeImmutable(),
            attachableId: 42,
            attachableType: 'plant',
            description: 'Test description'
        );

        $group = $this->createMock(Group::class);
        $this->groupService->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn($group);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Attachment::class));

        $expectedModel = $this->createMock(AttachmentModel::class);
        $this->repository->method('toModel')->willReturn($expectedModel);

        $result = $this->service->create($model);

        $this->assertSame($expectedModel, $result);
    }

    public function testCreateFromCreateImageDTOProcessesFileAndSaves(): void
    {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile->method('getMimeType')->willReturn('image/png');

        // 1. Добавляем groupId: 2 первым аргументом
        $dto = new CreateImageDTO(
            groupId: 2,
            imageFile: $uploadedFile,
            isShown: true,
            filename: '',
            path: '',
            mimeType: '',
            alt: 'Alt',
            title: 'Title',
            fileDate: new DateTimeImmutable(),
            attachableId: 55,
            attachableType: 'plant',
            description: 'Desc'
        );

        // 2. Настройка FileService
        $this->fileService->method('getAttachmentsPath')->willReturn('/attachments/plant/55/');

        $storedFile = $this->createMock(File::class);
        $storedFile->method('getFilename')->willReturn('generated_name.png');

        $this->fileService->expects($this->once())
            ->method('storeUploadedFile')
            ->willReturn($storedFile);

        // 3. Создаем реальную модель (т.к. она readonly) для возврата из фабрики
        $createModel = new CreateAttachmentModel(
            groupId: 2,
            isShown: true,
            filename: 'generated_name.png',
            path: '/attachments/plant/55/',
            mimeType: 'image/png',
            alt: 'Alt',
            title: 'Title',
            fileDate: $dto->fileDate,
            attachableId: 55,
            attachableType: 'plant',
            description: 'Desc'
        );

        $this->modelFactory->method('makeModel')->willReturn($createModel);

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->repository->method('toModel')->willReturn($this->createMock(AttachmentModel::class));

        // 4. Выполнение
        $result = $this->service->createFromCreateImageDTO($dto);

        // 5. Проверки
        $this->assertInstanceOf(AttachmentModel::class, $result);

        // Эти ассерты пройдут, если метод createFromCreateImageDTO изменяет свойства внутри $dto
        $this->assertEquals('image/png', $dto->mimeType);
        $this->assertEquals('generated_name.png', $dto->filename);
    }

    public function testUpdateChangesDisplaySettingsVisibility(): void
    {
        $updateModel = new UpdateAttachmentModel(
            groupId: 5,
            isShown: false, // Тестируем вызов hide()
            filename: 'upd.jpg',
            path: '/upd/',
            mimeType: 'image/jpeg',
            alt: 'alt',
            title: 'title',
            fileDate: new DateTimeImmutable(),
            attachableId: 1,
            attachableType: 'pest',
            description: 'desc'
        );

        $attachment = $this->createMock(Attachment::class);
        $group = $this->createMock(Group::class);
        $displaySettings = $this->createMock(DisplaySettings::class);

        $this->groupService->method('find')->willReturn($group);

        // Настройка Fluent Interface сущности
        $attachment->method('moveToGroup')->willReturn($attachment);
        $attachment->method('updateDisplaySettings')->willReturn($attachment);
        $attachment->method('updateFileInfo')->willReturn($attachment);
        $attachment->method('updateTarget')->willReturn($attachment);
        $attachment->method('getDisplaySettings')->willReturn($displaySettings);

        // Проверка: так как isShown = false, должен вызваться hide()
        $displaySettings->expects($this->once())->method('hide');

        $this->service->update($attachment, $updateModel);
    }

    public function testUpdateFromEditImageDTORemovesOldFileAndCallsUpdate(): void
    {
        $attachment = $this->createMock(Attachment::class);
        $fileInfo = $this->createMock(\App\Domain\ValueObject\Attachment\FileInfo::class);

        // Настраиваем FileInfo, чтобы сработало условие в removeOldFile
        $fileInfo->method('getPath')->willReturn('/uploads/');
        $fileInfo->method('getFilename')->willReturn('old_image.jpg');
        $attachment->method('getFileInfo')->willReturn($fileInfo);

        // Настройка цепочки вызовов (Fluent)
        $attachment->method('moveToGroup')->willReturn($attachment);
        $attachment->method('updateDisplaySettings')->willReturn($attachment);
        $attachment->method('updateFileInfo')->willReturn($attachment);
        $attachment->method('updateTarget')->willReturn($attachment);
        $attachment->method('getDisplaySettings')->willReturn($this->createMock(DisplaySettings::class));

        $dto = new EditImageDTO(
            groupId: 2,
            imageFile: $this->createMock(UploadedFile::class),
            isShown: true, title: 'T', filename: 'f.j', path: '/p/', mimeType: 'i/j',
            alt: 'a', fileDate: new DateTimeImmutable(), attachableId: 1,
            attachableType: 'plant', description: 'd'
        );

        $updateModel = new UpdateAttachmentModel(
            groupId: 10, isShown: true, filename: 'f.j', path: '/p/', mimeType: 'i/j',
            alt: 'a', title: 'T', fileDate: new DateTimeImmutable(), attachableId: 1,
            attachableType: 'plant', description: 'd'
        );

        $this->modelFactory->method('makeModel')->willReturn($updateModel);
        $this->groupService->method('find')->willReturn($this->createMock(Group::class));

        // Теперь вызовы точно произойдут
        $this->fileService->expects($this->once())->method('removeUploadedFile')->with('/uploads/old_image.jpg');
        $this->repository->expects($this->once())->method('update');

        $this->service->updateFromEditImageDTO($attachment, $dto);
    }

    public function testDeleteWithFileThrowsExceptionIfNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Вложение с ID 999 не найдено');

        $this->service->deleteWithFile(999);
    }

    public function testDeleteWithFileRemovesFileAndEntity(): void
    {
        $id = 123;
        $attachment = $this->createMock(Attachment::class);
        $fileInfo = $this->createMock(FileInfo::class);

        // Настраиваем данные файла, иначе removeOldFile ничего не сделает
        $fileInfo->method('getPath')->willReturn('/path/');
        $fileInfo->method('getFilename')->willReturn('file.jpg');
        $attachment->method('getFileInfo')->willReturn($fileInfo);

        $this->repository->method('find')->with($id)->willReturn($attachment);

        $this->fileService->expects($this->once())->method('removeUploadedFile')->with('/path/file.jpg');
        $this->repository->expects($this->once())->method('remove')->with($attachment);

        $this->service->deleteWithFile($id);
    }
}
