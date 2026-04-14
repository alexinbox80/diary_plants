<?php

namespace Unit\Infrastructure\Repository;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use PHPUnit\Framework\MockObject\MockObject;
use App\Infrastructure\Repository\MarkerRepository;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\MarkerRepositoryDecorator;

class MarkerRepositoryDecoratorTest extends TestCase
{
    private MarkerRepository|MockObject $markerRepository;
    private GroupRepositoryDecorator|MockObject $groupRepository;
    private MarkerRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->markerRepository = $this->createMock(MarkerRepository::class);
        $this->groupRepository = $this->createMock(GroupRepositoryDecorator::class);
        $this->decorator = new MarkerRepositoryDecorator(
            $this->groupRepository,
            $this->markerRepository
        );
    }

    public function testGetMarkersForDairyReturnsFormattedArrays(): void
    {
        // 1. Готовим сущность-заглушку
        $marker = $this->createMock(Marker::class);
        $marker->method('getId')->willReturn(1);
        $marker->method('getLetter')->willReturn('FE');
        $marker->method('getDescription')->willReturn('Удобрение');
        $marker->method('getColor')->willReturn('#00FF00');
        $marker->method('getType')->willReturn(AttachableType::FERTILIZER);

        $this->markerRepository->expects($this->once())
            ->method('getMarkersForDairy')
            ->with(10, 'fertilizer')
            ->willReturn([$marker]);

        // 2. Вызываем метод
        $result = $this->decorator->getMarkersForDairy(10, 'fertilizer');

        // 3. Проверяем формат массива для дневника
        $this->assertCount(1, $result);
        $this->assertEquals([
            'id' => 1,
            'letter' => 'FE',
            'description' => 'Удобрение',
            'color' => '#00FF00',
            'type' => 'fertilizer',
        ], $result[0]);
    }

    public function testFindModelReturnsMarkerModel(): void
    {
        $marker = $this->createMock(Marker::class);

        // Явно указываем Enum, чтобы MarkerModel::fromEntity не упал
        $marker->method('getType')->willReturn(AttachableType::FERTILIZER);
        // Добавьте другие геттеры, если fromEntity их использует (letter, color и т.д.)
        $marker->method('getLetter')->willReturn('FE');
        $marker->method('getColor')->willReturn('#00FF00');

        $this->markerRepository->method('find')->with(1)->willReturn($marker);

        $result = $this->decorator->findModel(1);

        $this->assertInstanceOf(MarkerModel::class, $result);
    }

    public function testGetMarkersPaginatedThrowsExceptionOnInvalidData(): void
    {
        // Эмулируем ситуацию, когда основной репозиторий вернул не массив в ключе 'items'
        $this->markerRepository->method('getMarkersPaginated')
            ->willReturn(['items' => 'not_an_array', 'pagination' => []]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array for Markers');

        $this->decorator->getMarkersPaginated(1, 10);
    }

    public function testToModelWithRelationsCallsGroupRepository(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);

        // Настройка мока Marker
        $marker->method('getGroup')->willReturn($group);
        $marker->method('getType')->willReturn(AttachableType::FERTILIZER);
        $marker->method('getLetter')->willReturn('FE');
        $marker->method('getColor')->willReturn('#00FF00');

        $groupModel = $this->createMock(GroupModel::class);

        $this->groupRepository->expects($this->once())
            ->method('toModel')
            ->with($group)
            ->willReturn($groupModel);

        $result = $this->decorator->toModel($marker, true);

        $this->assertInstanceOf(MarkerModel::class, $result);
    }

    public function testCreateDelegatesToRepository(): void
    {
        $marker = $this->createMock(Marker::class);
        $this->markerRepository->expects($this->once())
            ->method('create')
            ->with($marker)
            ->willReturn(123);

        $result = $this->decorator->create($marker);
        $this->assertEquals(123, $result);
    }
}
