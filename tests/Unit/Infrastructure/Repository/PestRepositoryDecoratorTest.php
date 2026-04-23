<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Marker\MarkerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Infrastructure\Repository\PestRepository;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Infrastructure\Repository\PestRepositoryDecorator;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\MarkerRepositoryDecorator;

#[CoversClass(PestRepositoryDecorator::class)]
class PestRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private MarkerRepositoryDecorator|MockObject $markerRepo;
    private PestRepository|MockObject $innerRepository;
    private PestRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->markerRepo = $this->createMock(MarkerRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(PestRepository::class);

        $this->decorator = new PestRepositoryDecorator(
            $this->groupRepo,
            $this->markerRepo,
            $this->innerRepository
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $pestId = 1;
        $pest = $this->createPestMock($pestId);

        $this->innerRepository->method('find')->with($pestId)->willReturn($pest);

        // По умолчанию addRelations = false, репозитории связей не вызываются
        $result = $this->decorator->findModel($pestId);

        $this->assertInstanceOf(PestModel::class, $result);
        $this->assertSame($pestId, $result->getId());
    }

    #[Test]
    public function testGetPestsForDairyReturnsModelsWithRelations(): void
    {
        $groupId = 10;
        $pest = $this->createPestMock(1);

        $this->innerRepository->method('getPestsForDairy')
            ->with($groupId)
            ->willReturn([$pest]);

        // Настраиваем возврат моделей для связей (так как в методе передается true)
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->markerRepo->method('toModel')->willReturn($this->createMock(MarkerModel::class));

        $results = $this->decorator->getPestsForDairy($groupId);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(PestModel::class, $results[0]);
    }

    #[Test]
    public function testGetPestsPaginatedThrowsExceptionOnInvalidItems(): void
    {
        $this->innerRepository->method('getPestsPaginated')
            ->willReturn(['items' => 'not_an_array']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array for pests');

        $this->decorator->getPestsPaginated(1, 10);
    }

    /**
     * Создает мок Pest через Stub для совместимости с PHPUnit 12
     */
    private function createPestMock(int $id): MockObject
    {
        $pest = $this->getMockBuilder(PestStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getMarker', 'getTitle',
                'getVolume', 'getDetails', 'getCreatedAt', 'getUpdatedAt'
            ])
            ->getMock();

        // Важно: getAmount -> int, getApplicationRate -> ?string (согласно вашим типам в VO)
        $volume = $this->createMock(PreparationVolume::class);
        $volume->method('getAmount')->willReturn(100);
        $volume->method('getApplicationRate')->willReturn('5ml/l');

        $details = $this->createMock(PreparationDetails::class);
        $details->method('getManufacturer')->willReturn('AgroBio');
        $details->method('getDescription')->willReturn('Test Desc');
        $details->method('getComment')->willReturn('Test Comment');

        $pest->method('getId')->willReturn($id);
        $pest->method('getTitle')->willReturn('Anti-Pest');
        $pest->method('getVolume')->willReturn($volume);
        $pest->method('getDetails')->willReturn($details);
        $pest->method('getGroup')->willReturn($this->createMock(Group::class));
        $pest->method('getMarker')->willReturn($this->createMock(Marker::class));

        $now = new DateTimeImmutable();
        $pest->method('getCreatedAt')->willReturn($now);
        $pest->method('getUpdatedAt')->willReturn($now);

        return $pest;
    }
}

/**
 * Вспомогательный класс для решения проблем с наследованием и трейтами в моках
 */
abstract class PestStub extends Pest
{
    public function getTitle(): string { return ''; }
    public function getVolume(): PreparationVolume { return $this->createMock(PreparationVolume::class); }
    public function getDetails(): PreparationDetails { return $this->createMock(PreparationDetails::class); }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
