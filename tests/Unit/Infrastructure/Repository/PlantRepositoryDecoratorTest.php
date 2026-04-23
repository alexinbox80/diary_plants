<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\OId;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use Doctrine\Common\Collections\Collection;
use App\Domain\ValueObject\Plant\SalesInfo;
use App\Domain\ValueObject\Plant\LifeCycle;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\ValueObject\Plant\PlantIdentifier;
use App\Infrastructure\Repository\PlantRepository;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\Repository\WateringRepositoryInterface;
use App\Domain\Repository\StimulantRepositoryInterface;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\PlantRepositoryDecorator;

#[CoversClass(PlantRepositoryDecorator::class)]
class PlantRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private PlantRepository|MockObject $innerRepository;
    private FertilizerRepositoryInterface|MockObject $fertilizerRepo;
    private PestRepositoryInterface|MockObject $pestRepo;
    private StimulantRepositoryInterface|MockObject $stimulantRepo;
    private WateringRepositoryInterface|MockObject $wateringRepo;
    private PlantRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->innerRepository = $this->createMock(PlantRepository::class);
        $this->fertilizerRepo = $this->createMock(FertilizerRepositoryInterface::class);
        $this->pestRepo = $this->createMock(PestRepositoryInterface::class);
        $this->stimulantRepo = $this->createMock(StimulantRepositoryInterface::class);
        $this->wateringRepo = $this->createMock(WateringRepositoryInterface::class);

        $this->decorator = new PlantRepositoryDecorator(
            $this->groupRepo,
            $this->innerRepository,
            $this->fertilizerRepo,
            $this->pestRepo,
            $this->stimulantRepo,
            $this->wateringRepo
        );
    }

    public function testFindModelReturnsCorrectModelWithRelations(): void
    {
        $id = 1;
        $plant = $this->createPlantMock($id);

        $this->innerRepository->method('find')->with($id)->willReturn($plant);

        // Мокаем зависимости, которые вызываются при addRelations = true
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(PlantModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    public function testGetPlantsPaginatedReturnsFormattedArray(): void
    {
        $plant = $this->createPlantMock(1);
        $this->innerRepository->method('getPlantsPaginatedWithAttachments')->willReturn([
            'items' => [$plant],
            'pagination' => ['total' => 1]
        ]);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        $result = $this->decorator->getPlantsPaginated(1, 10);

        $this->assertArrayHasKey('plantsModel', $result);
        $this->assertInstanceOf(PlantModel::class, $result['plantsModel'][0]);
    }

    /**
     * Фабрика мока Plant
     */
    private function createPlantMock(int $id): MockObject
    {
        // Используем стаб для перехвата геттеров типизированных свойств
        $plant = $this->getMockBuilder(PlantStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getTitle', 'getRoom', 'getPlantIdentifier',
                'getLifeCycle', 'getPurchaseInfo', 'getSalesInfo', 'getAnalytic',
                'getUsages', 'getOffsprings', 'getRepottings', 'getLoadedAttachments',
                'getCreatedAt', 'getUpdatedAt', 'isShown', 'getDescription', 'getComment'
            ])
            ->getMock();

        // Настройка идентификатора: используем реальный OId, так как он final
        $oid = OId::next();
        $identifier = $this->createMock(PlantIdentifier::class);
        $identifier->method('getOid')->willReturn($oid);

        // Для коллекций используем ArrayCollection.
        // В декораторе условие (instanceof PersistentCollection) вернет false,
        // и код безопасно пропустит обработку вложенных элементов.
        $emptyCollection = new ArrayCollection();

        // Настройка возвращаемых значений мока
        $plant->method('getId')->willReturn($id);
        $plant->method('getTitle')->willReturn('Lemon Eureka');
        $plant->method('getRoom')->willReturn('Kitchen');
        $plant->method('isShown')->willReturn(true);
        $plant->method('getPlantIdentifier')->willReturn($identifier);

        // Мокаем вложенные Value Objects
        $plant->method('getLifeCycle')->willReturn($this->createMock(LifeCycle::class));
        $plant->method('getPurchaseInfo')->willReturn($this->createMock(PurchaseInfo::class));
        $plant->method('getSalesInfo')->willReturn($this->createMock(SalesInfo::class));

        $plant->method('getUsages')->willReturn($emptyCollection);
        $plant->method('getOffsprings')->willReturn($emptyCollection);
        $plant->method('getRepottings')->willReturn($emptyCollection);
        $plant->method('getLoadedAttachments')->willReturn([]);
        $plant->method('getAnalytic')->willReturn(null);
        $plant->method('getGroup')->willReturn($this->createMock(Group::class));

        $now = new \DateTimeImmutable();
        $plant->method('getCreatedAt')->willReturn($now);
        $plant->method('getUpdatedAt')->willReturn($now);

        return $plant;
    }
}

/**
 * Стаб для обхода ограничений PHPUnit 12 на инициализацию типизированных свойств.
 */
abstract class PlantStub extends Plant
{
    public function getId(): int { return 0; }
    public function getGroup(): Group { return Assert::createMock(Group::class); }
    public function getPlantIdentifier(): PlantIdentifier { return Assert::createMock(PlantIdentifier::class); }
    public function getLifeCycle(): LifeCycle { return Assert::createMock(LifeCycle::class); }
    public function getPurchaseInfo(): PurchaseInfo { return Assert::createMock(PurchaseInfo::class); }
    public function getSalesInfo(): SalesInfo { return Assert::createMock(SalesInfo::class); }
    public function getUsages(): Collection { return new ArrayCollection(); }
    public function getOffsprings(): Collection { return new ArrayCollection(); }
    public function getRepottings(): Collection { return new ArrayCollection(); }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
