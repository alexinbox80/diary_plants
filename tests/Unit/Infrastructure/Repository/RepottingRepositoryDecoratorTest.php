<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Repotting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Infrastructure\Repository\RepottingRepository;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\PlantRepositoryDecorator;
use App\Infrastructure\Repository\RepottingRepositoryDecorator;

#[CoversClass(RepottingRepositoryDecorator::class)]
class RepottingRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private PlantRepositoryDecorator|MockObject $plantRepo;
    private RepottingRepository|MockObject $innerRepository;
    private RepottingRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->plantRepo = $this->createMock(PlantRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(RepottingRepository::class);

        $this->decorator = new RepottingRepositoryDecorator(
            $this->groupRepo,
            $this->plantRepo,
            $this->innerRepository
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $id = 1;
        $repotting = $this->createRepottingMock($id);

        $this->innerRepository->method('find')->with($id)->willReturn($repotting);

        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(RepottingModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    private function createRepottingMock(int $id): MockObject
    {
        $repotting = $this->getMockBuilder(RepottingStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getPlant', 'getRepottedAt',
                'getDetails', 'getCreatedAt', 'getUpdatedAt', 'getComment', 'getSubstrate'
            ])
            ->getMock();

        $details = $this->createMock(RepottingDetails::class);

        // Исправлено: методы getType(), getMaterial(), getPotSize() согласно классу RepottingDetails
        $details->method('getType')->willReturn(RepottingType::ROOT_PRUNING);
        $details->method('getMaterial')->willReturn(PotMaterial::PLASTIC);
        $details->method('getPotSize')->willReturn('15cm');

        $now = new DateTimeImmutable();

        $repotting->method('getId')->willReturn($id);
        $repotting->method('getRepottedAt')->willReturn($now);
        $repotting->method('getDetails')->willReturn($details);
        $repotting->method('getComment')->willReturn('Comment');
        $repotting->method('getSubstrate')->willReturn('Soil');
        $repotting->method('getGroup')->willReturn($this->createMock(Group::class));
        $repotting->method('getPlant')->willReturn($this->createMock(Plant::class));
        $repotting->method('getCreatedAt')->willReturn($now);
        $repotting->method('getUpdatedAt')->willReturn($now);

        return $repotting;
    }
}

/**
 * Исправленный Стаб
 */
abstract class RepottingStub extends Repotting
{
    public function getId(): int { return 0; }
    public function getRepottedAt(): DateTimeImmutable { return new DateTimeImmutable(); }

    // Возвращаем мок, так как конструктор RepottingDetails требует аргументы
    public function getDetails(): RepottingDetails {
        /** @var RepottingDetails|MockObject $mock */
        $mock = Assert::createMock(RepottingDetails::class);
        return $mock;
    }

    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getComment(): ?string { return null; }
    public function getSubstrate(): ?string { return null; }
}
