<?php

namespace Unit\Domain\Model\Stimulant;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Stimulant;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class StimulantModelTest extends TestCase
{
    public function testFromEntityAndToArray(): void
    {
        // Устанавливаем московскую таймзону, чтобы время в тесте и модели совпадало
        $timezone = new DateTimeZone('Europe/Moscow');
        $now = new DateTimeImmutable('2024-01-15 15:00:00', $timezone);

        // 1. Создаем реальные Value Objects компонентов (PreparationVolume и PreparationDetails)
        $volume = new PreparationVolume(
            amount: 500,
            applicationRate: '5мл на 1л'
        );

        $details = new PreparationDetails(
            manufacturer: 'Bayer',
            description: 'Ускоритель роста',
            comment: 'Хранить в тени'
        );

        // 2. Мокаем связанные сущности (Group и Marker)
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        $markerEntity = $this->createMock(Marker::class);
        $markerEntity->method('getId')->willReturn(77);

        // 3. Мокаем основную сущность Stimulant
        $stimulant = $this->createMock(Stimulant::class);
        $stimulant->method('getId')->willReturn(10);
        $stimulant->method('getTitle')->willReturn('Эпин-Экстра');
        $stimulant->method('getGroup')->willReturn($groupEntity);
        $stimulant->method('getMarker')->willReturn($markerEntity);
        $stimulant->method('getVolume')->willReturn($volume);
        $stimulant->method('getDetails')->willReturn($details);
        $stimulant->method('getCreatedAt')->willReturn($now);
        $stimulant->method('getUpdatedAt')->willReturn($now);

        // 4. Мокаем модели (DTO), которые передаются в фабричный метод
        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Удобрения');

        $markerModel = $this->createMock(MarkerModel::class);
        $markerModel->method('getLetter')->willReturn('E');
        $markerModel->method('getColor')->willReturn('#00FF00');

        // 5. Создаем тестируемую модель через fromEntity
        $model = StimulantModel::fromEntity($stimulant, $groupModel, $markerModel);

        // 6. Проверки (Assertions)
        $this->assertEquals(10, $model->getId());
        $this->assertEquals('Эпин-Экстра', $model->getTitle());
        $this->assertEquals(500, $model->getAmount());

        $array = $model->toArray();

        $this->assertEquals('Удобрения', $array['group_title']);
        $this->assertEquals('E', $array['marker_letter']);
        $this->assertEquals('#00FF00', $array['marker_color']);
        $this->assertEquals('Bayer', $array['manufacturer']);

        // Теперь это сравнение пройдет успешно, так как таймзоны идентичны
        $this->assertEquals('15.01.2024 15:00:00', $array['created_at']);
        $this->assertEquals('15.01.2024 15:00:00', $array['updated_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = StimulantModel::getTableHeaderRu();
        $this->assertArrayHasKey('application_rate', $headers);
        $this->assertEquals('Норма расхода', $headers['application_rate']);
    }
}
