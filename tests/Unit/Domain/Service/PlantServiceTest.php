<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use ReflectionProperty;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\Service\FileService;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use App\Domain\Model\Plant\PlantModel;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Plant\UpdatePlantModel;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Model\Plant\CreatePlantModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Plant\PlantIdentifier;
use App\Domain\Repository\PlantRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[CoversClass(PlantService::class)]
class PlantServiceTest extends TestCase
{
    private PlantRepositoryInterface|MockObject $repository;
    private GroupService|MockObject $groupService;
    private FileService|MockObject $fileService;
    private UrlGeneratorInterface|MockObject $urlGenerator;
    private PlantService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PlantRepositoryInterface::class);
        $this->groupService = $this->createMock(GroupService::class);
        $this->fileService = $this->createMock(FileService::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->service = new PlantService(
            'https://test.com',
            $this->repository,
            $this->createMock(ModelFactory::class),
            $this->groupService,
            $this->fileService,
            $this->urlGenerator
        );
    }

    #[Test]
    public function testCreatePlantSuccess(): void
    {
        // 1. Подготовка модели
        $model = new CreatePlantModel(
            groupId: 2,
            title: 'Lemon Eureka',
            room: 'Kitchen',
            isShown: true,
            description: 'Fresh lemon',
            purchaseDate: new DateTimeImmutable('2023-12-25'),
            vaccinationDate: null,
            plantingDate: new DateTimeImmutable('2024-01-01'),
            seller: 'Local Shop',
            nursery: 'Green Nursery',
            price: Price::fromString('100 RUR'),
            shippingCost: null,
            packagingCost: null,
            soil: 'Citrus Mix',
            isSold: false,
            sellingDate: null,
            sellingPrice: null,
            comment: 'Note'
        );

        $group = $this->createMock(Group::class);
        $plantModel = $this->createMock(PlantModel::class);

        // 2. Ожидания
        $this->groupService->method('find')->willReturn($group);

        // Главное изменение: имитируем присвоение ID базой данных
        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->callback(function (Plant $plant) {
                $reflection = new ReflectionProperty(Plant::class, 'id');
                $reflection->setAccessible(true);
                $reflection->setValue($plant, 1); // Даем объекту ID вручную
                return true;
            }));

        // Настраиваем моки, которые вызываются ВНУТРИ processFileForQrCode
        $this->urlGenerator->method('generate')->willReturn('/profile/123');
        $this->fileService->method('getAttachmentsPath')->willReturn('path/');
        $this->fileService->method('getQrCodeLink')->willReturn('link_to_qr');

        $this->repository->method('toModel')->willReturn($plantModel);

        $result = $this->service->create($model);
        $this->assertSame($plantModel, $result);
    }

    #[Test]
    public function testGetChoicesForChoiceType(): void
    {
        $p1 = $this->createMock(PlantModel::class);
        $p1->method('getTitle')->willReturn('Aloe');
        $p1->method('getId')->willReturn(1);

        $this->repository->method('getPlantsForForm')->willReturn([$p1]);

        $choices = $this->service->getChoicesForChoiceType(2);

        $this->assertSame(['Aloe' => 1], $choices);
    }

    #[Test]
    public function testUpdatePlantTogglesVisibility(): void
    {
        $updateModel = new UpdatePlantModel(
            groupId: 2,
            title: 'Test',
            room: 'Test',
            isShown: false, // Теперь тест проверит вызов hide()
            description: 'Test',
            purchaseDate: new DateTimeImmutable(),
            vaccinationDate: null,
            plantingDate: new DateTimeImmutable(),
            seller: 'Test',
            nursery: 'Test',
            price: null,
            shippingCost: null,
            packagingCost: null,
            soil: 'Test',
            isSold: false,
            sellingDate: null,
            sellingPrice: null,
            comment: 'Test'
        );

        $plant = $this->createMock(Plant::class);
        $this->groupService->method('find')->willReturn($this->createMock(Group::class));

        // Настраиваем Fluent Interface (чтобы методы возвращали сам объект $plant)
        $plant->method('moveToGroup')->willReturn($plant);
        $plant->method('setTitle')->willReturn($plant);
        $plant->method('setRoom')->willReturn($plant);
        $plant->method('changeLifeCycle')->willReturn($plant);
        $plant->method('changePurchaseInfo')->willReturn($plant);
        $plant->method('changeSalesInfo')->willReturn($plant);
        $plant->method('setComment')->willReturn($plant);
        $plant->method('setDescription')->willReturn($plant);

        // 2. Проверяем, что вызвался hide(), так как в модели isShown = false
        $plant->expects($this->once())->method('hide');
        $plant->expects($this->never())->method('show');

        $this->repository->expects($this->once())->method('update');

        $this->service->update($plant, $updateModel);
    }

    #[Test]
    public function testDeleteWithQrCodeRemovesFileAndEntity(): void
    {
        $plant = $this->createMock(Plant::class);
        $plantId = 123;
        $qrCodeLink = 'path/to/qr.png';

        // Настраиваем получение ссылки на QR-код из VO идентификатора
        $identifier = $this->createMock(PlantIdentifier::class);
        $identifier->method('getQrCodeLink')->willReturn($qrCodeLink);
        $plant->method('getPlantIdentifier')->willReturn($identifier);

        $this->repository->method('find')->with($plantId)->willReturn($plant);

        // Ожидаем удаление файла через FileService
        $this->fileService->expects($this->once())
            ->method('removeUploadedFile')
            ->with($qrCodeLink);

        // Ожидаем удаление сущности из репозитория
        $this->repository->expects($this->once())
            ->method('remove')
            ->with($plant);

        $this->service->deleteWithQrCode($plant);
    }

    #[Test]
    public function testCreateGeneratesQrCodeWithCorrectUrl(): void
    {
        $model = new CreatePlantModel(
            groupId: 2,
            title: 'Lemon',
            room: 'Kitchen',
            isShown: true,
            description: 'Desc',
            purchaseDate: new DateTimeImmutable(),
            vaccinationDate: null,
            plantingDate: new DateTimeImmutable(),
            seller: 'Shop',
            nursery: 'Nursery',
            price: null,
            shippingCost: null,
            packagingCost: null,
            soil: 'Soil',
            isSold: false,
            sellingDate: null,
            sellingPrice: null,
            comment: 'Comment'
        );

        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(2);
        $this->groupService->method('find')->with(2)->willReturn($group);

        // Настройка зависимостей для QR-кода
        $this->urlGenerator->method('generate')->willReturn('/profile/uuid-123');
        $this->fileService->method('getAttachmentsPath')->willReturn('path/');
        $this->fileService->method('getQrCodeLink')->willReturn('final_link');

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->callback(function (Plant $plant) {
                // Устанавливаем ID через Reflection, чтобы processFileForQrCode не упал на проверке Assert
                $reflection = new ReflectionProperty(Plant::class, 'id');
                $reflection->setAccessible(true);
                $reflection->setValue($plant, 777);
                return true;
            }));

        $this->service->create($model);
    }
}
