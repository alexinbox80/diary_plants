<?php

namespace Unit\Domain\Model\Analytic;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Analytic\CreateAnalyticModel;

#[CoversClass(CreateAnalyticModel::class)]
class CreateAnalyticModelTest extends TestCase
{
    #[Test]
    public function testConstructorSetsProperties(): void
    {
        $plantId = 10;
        $groupId = 5;
        $count = 2;
        $averageDays = 14.5;

        $model = new CreateAnalyticModel($plantId, $groupId, $count, $averageDays);

        $this->assertSame($plantId, $model->plantId);
        $this->assertSame($groupId, $model->groupId);
        $this->assertSame($count, $model->count);
        $this->assertSame($averageDays, $model->averageDays);
    }

    #[Test]
    public function testDefaultValues(): void
    {
        $model = new CreateAnalyticModel(1, 2);

        $this->assertSame(0, $model->count);
        $this->assertSame(0.0, $model->averageDays);
    }
}
