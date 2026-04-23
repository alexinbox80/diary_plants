<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Marker\MarkerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Infrastructure\Repository\StimulantRepository;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\MarkerRepositoryDecorator;
use App\Infrastructure\Repository\StimulantRepositoryDecorator;

#[CoversClass(StimulantRepositoryDecorator::class)]
class StimulantRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private MarkerRepositoryDecorator|MockObject $markerRepo;
    private StimulantRepository|MockObject $innerRepository;
    private StimulantRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->markerRepo = $this->createMock(MarkerRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(StimulantRepository::class);

        $this->decorator = new StimulantRepositoryDecorator(
            $this->groupRepo,
            $this->markerRepo,
            $this->innerRepository
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $id = 1;
        $stimulant = $this->createStimulantMock($id);

        $this->innerRepository->method('find')->with($id)->willReturn($stimulant);

        // По умолчанию addRelations = false
        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(StimulantModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    #[Test]
    public function testGetStimulantsForDairyReturnsModelsWithRelations(): void
    {
        $groupId = 5;
        $stimulant = $this->createStimulantMock(10);

        $this->innerRepository->method('getStimulantsForDairy')
            ->with($groupId)
            ->willReturn([$stimulant]);

        // Настраиваем возврат моделей связей
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->markerRepo->method('toModel')->willReturn($this->createMock(MarkerModel::class));

        $results = $this->decorator->getStimulantsForDairy($groupId);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(StimulantModel::class, $results[0]);
    }

    #[Test]
    public function testGetStimulantsPaginatedThrowsExceptionOnInvalidItems(): void
    {
        $this->innerRepository->method('getStimulantsPaginated')
            ->willReturn(['items' => 'not_an_array']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array for stimulants');

        $this->decorator->getStimulantsPaginated(1, 10);
    }

    /**
     * Создает мок Stimulant через Stub для PHPUnit 12
     */
    private function createStimulantMock(int $id): MockObject
    {
        $stimulant = $this->getMockBuilder(StimulantStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getMarker', 'getTitle',
                'getVolume', 'getDetails', 'getCreatedAt', 'getUpdatedAt'
            ])
            ->getMock();

        // Настройка вложенных VO. getAmount -> int, getApplicationRate -> ?string
        $volume = $this->createMock(PreparationVolume::class);
        $volume->method('getAmount')->willReturn(50);
        $volume->method('getApplicationRate')->willReturn('2ml/l');

        $details = $this->createMock(PreparationDetails::class);
        $details->method('getManufacturer')->willReturn('GrowStrong');
        $details->method('getDescription')->willReturn('Root booster');
        $details->method('getComment')->willReturn('Morning use only');

        // Настраиваем возвраты
        $stimulant->method('getId')->willReturn($id);
        $stimulant->method('getTitle')->willReturn('Stimulant Title');
        $stimulant->method('getVolume')->willReturn($volume);
        $stimulant->method('getDetails')->willReturn($details);
        $stimulant->method('getGroup')->willReturn($this->createMock(Group::class));
        $stimulant->method('getMarker')->willReturn($this->createMock(Marker::class));

        $now = new DateTimeImmutable();
        $stimulant->method('getCreatedAt')->willReturn($now);
        $stimulant->method('getUpdatedAt')->willReturn($now);

        return $stimulant;
    }
}

/**
 * Стаб для обхода типизированных свойств Preparation и трейтов
 */
abstract class StimulantStub extends \App\Domain\Entity\Stimulant
{
    public function getTitle(): string { return ''; }
    public function getVolume(): PreparationVolume { return $this->createMock(PreparationVolume::class); }
    public function getDetails(): PreparationDetails { return $this->createMock(PreparationDetails::class); }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
