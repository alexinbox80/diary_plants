<?php

namespace Unit\Domain\Model\Analytic;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Analytic\UpdateAnalyticModel;

#[CoversClass(UpdateAnalyticModel::class)]
class UpdateAnalyticModelTest extends TestCase
{
    #[Test]
    public function testConstructorSetsProperties(): void
    {
        $plantId = 101;
        $groupId = 20;
        $count = 5;
        $averageDays = 12.3;

        $model = new UpdateAnalyticModel($plantId, $groupId, $count, $averageDays);

        $this->assertSame($plantId, $model->plantId);
        $this->assertSame($groupId, $model->groupId);
        $this->assertSame($count, $model->count);
        $this->assertSame($averageDays, $model->averageDays);
    }

    #[Test]
    public function testDefaultValues(): void
    {
        // Проверяем работу опциональных параметров
        $model = new UpdateAnalyticModel(1, 2);

        $this->assertSame(0, $model->count);
        $this->assertSame(0.0, $model->averageDays);
    }

    #[Test]
    public function testReadonlyProperties(): void
    {
        $model = new UpdateAnalyticModel(1, 2);

        $this->expectException(\Error::class);
        // Попытка изменения readonly свойства вызовет Error в PHP 8.1+
        $model->plantId = 10;
    }
}
