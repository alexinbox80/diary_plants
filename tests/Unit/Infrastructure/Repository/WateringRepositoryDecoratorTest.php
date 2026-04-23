<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Watering\WateringModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Infrastructure\Repository\WateringRepository;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\MarkerRepositoryDecorator;
use App\Infrastructure\Repository\WateringRepositoryDecorator;

#[CoversClass(WateringRepositoryDecorator::class)]
class WateringRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private MarkerRepositoryDecorator|MockObject $markerRepo;
    private WateringRepository|MockObject $innerRepository;
    private WateringRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->markerRepo = $this->createMock(MarkerRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(WateringRepository::class);

        $this->decorator = new WateringRepositoryDecorator(
            $this->groupRepo,
            $this->markerRepo,
            $this->innerRepository
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $id = 1;
        $watering = $this->createWateringMock($id);

        $this->innerRepository->method('find')->with($id)->willReturn($watering);

        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(WateringModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    #[Test]
    public function testGetWateringsForDairyReturnsModelsWithRelations(): void
    {
        $groupId = 5;
        $watering = $this->createWateringMock(10);

        $this->innerRepository->method('getWateringsForDairy')
            ->with($groupId)
            ->willReturn([$watering]);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->markerRepo->method('toModel')->willReturn($this->createMock(MarkerModel::class));

        $results = $this->decorator->getWateringsForDairy($groupId);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(WateringModel::class, $results[0]);
    }

    private function createWateringMock(int $id): MockObject
    {
        $watering = $this->getMockBuilder(WateringStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId',
                'getGroup',
                'getMarker',
                'getDetails',
                'getCreatedAt',
                'getUpdatedAt'
            ])
            ->getMock();

        $details = $this->createMock(WateringDetails::class);

        // Настраиваем методы согласно вашему классу WateringDetails
        $details->method('getAmount')->willReturn(250);
        $details->method('getType')->willReturn(WaterType::FILTERED);
        $details->method('getMethod')->willReturn(WateringMethod::TOP);
        $details->method('getTemperature')->willReturn(22.5);

        $watering->method('getId')->willReturn($id);
        $watering->method('getDetails')->willReturn($details);
        $watering->method('getGroup')->willReturn($this->createMock(Group::class));
        $watering->method('getMarker')->willReturn($this->createMock(Marker::class));

        $now = new DateTimeImmutable();
        $watering->method('getCreatedAt')->willReturn($now);
        $watering->method('getUpdatedAt')->willReturn($now);

        return $watering;
    }
}

/**
 * Стаб для PHPUnit 12, чтобы видеть методы из трейтов и родительских определений
 */
abstract class WateringStub extends Watering
{
    public function getId(): int { return 0; }
    public function getDetails(): WateringDetails {
        return new WateringDetails(0);
    }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
