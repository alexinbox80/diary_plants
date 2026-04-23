<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Fertilizer;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Infrastructure\Repository\FertilizerRepository;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\MarkerRepositoryDecorator;
use App\Infrastructure\Repository\FertilizerRepositoryDecorator;

#[CoversClass(FertilizerRepositoryDecorator::class)]
class FertilizerRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private MarkerRepositoryDecorator|MockObject $markerRepo;
    private FertilizerRepository|MockObject $innerRepository;
    private FertilizerRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->markerRepo = $this->createMock(MarkerRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(FertilizerRepository::class);

        $this->decorator = new FertilizerRepositoryDecorator(
            $this->groupRepo,
            $this->markerRepo,
            $this->innerRepository
        );
    }

    #[Test]
    public function testFindModelReturnsModelWithoutRelationsByDefault(): void
    {
        $fertilizerId = 1;
        $fertilizer = $this->createFertilizerMock($fertilizerId);

        $this->innerRepository->method('find')->with($fertilizerId)->willReturn($fertilizer);

        $result = $this->decorator->findModel($fertilizerId);

        $this->assertInstanceOf(FertilizerModel::class, $result);
        $this->assertSame($fertilizerId, $result->getId());
    }

    #[Test]
    public function testGetFertilizersForDairyReturnsModelsWithRelations(): void
    {
        $groupId = 10;
        $fertilizer = $this->createFertilizerMock(1);

        $this->innerRepository->method('getfertilizersForDairy')
            ->with($groupId)
            ->willReturn([$fertilizer]);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->markerRepo->method('toModel')->willReturn($this->createMock(MarkerModel::class));

        $results = $this->decorator->getFertilizersForDairy($groupId);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(FertilizerModel::class, $results[0]);
    }

    /**
     * Создаем мок через вспомогательный класс-заглушку
     */
    private function createFertilizerMock(int $id): \PHPUnit\Framework\MockObject\MockObject
    {
        // Используем FertilizerStub и правильные имена методов из класса Preparation
        $fertilizer = $this->getMockBuilder(FertilizerStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId',
                'getGroup',
                'getMarker',
                'getTitle',
                'getVolume',  // ИСПРАВЛЕНО: было getPreparationVolume
                'getDetails', // ИСПРАВЛЕНО: было getPreparationDetails
                'getCreatedAt',
                'getUpdatedAt'
            ])
            ->getMock();

        // Мокаем VO объема
        $volume = $this->createMock(PreparationVolume::class);
        $volume->method('getAmount')->willReturn(500);
        $volume->method('getApplicationRate')->willReturn('10');

        // Мокаем VO деталей
        $details = $this->createMock(PreparationDetails::class);
        $details->method('getManufacturer')->willReturn('EcoCorp');
        $details->method('getDescription')->willReturn('Desc');
        $details->method('getComment')->willReturn('Comment');

        // Настраиваем возвраты геттеров
        $fertilizer->method('getId')->willReturn($id);
        $fertilizer->method('getTitle')->willReturn('Nitro-Grow');
        $fertilizer->method('getVolume')->willReturn($volume);
        $fertilizer->method('getDetails')->willReturn($details);

        $fertilizer->method('getGroup')->willReturn($this->createMock(Group::class));
        $fertilizer->method('getMarker')->willReturn($this->createMock(Marker::class));

        $now = new DateTimeImmutable();
        $fertilizer->method('getCreatedAt')->willReturn($now);
        $fertilizer->method('getUpdatedAt')->willReturn($now);

        return $fertilizer;
    }
}

/**
 * Вспомогательный класс для решения проблем с наследованием в PHPUnit 12.
 * Мы явно прописываем методы, которые PHPUnit не может найти в родителе или трейтах.
 */
abstract class FertilizerStub extends Fertilizer
{
    public function getTitle(): string { return ''; }

    // Имена должны быть как в родительском Preparation
    public function getVolume(): PreparationVolume {
        // Этот код не выполнится, так как метод в onlyMethods,
        // но сигнатура нужна для рефлексии PHPUnit
        return new PreparationVolume(0, '');
    }

    public function getDetails(): PreparationDetails {
        return new PreparationDetails();
    }

    public function getCreatedAt(): \DateTimeImmutable { return new \DateTimeImmutable(); }
    public function getUpdatedAt(): \DateTimeImmutable { return new \DateTimeImmutable(); }
}
