<?php

namespace Unit\Controller\Cli;

use App\Domain\Model\Analytic\AnalyticModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Service\AnalyticsCalculator;
use App\Domain\Service\AnalyticService;
use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
use App\Infrastructure\Console\InitWateringStatsCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InitWateringStatsCommandTest extends TestCase
{
    public function testExecuteSuccess(): void
    {
        // 1. Мокаем сервисы
        $plantService = $this->createMock(PlantService::class);
        $usageService = $this->createMock(UsageService::class);
        $analyticService = $this->createMock(AnalyticService::class);
        $calculator = new AnalyticsCalculator(); // Можно использовать реальный, если он простой

        // 2. Подготовка данных (PlantModel)
        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getId')->willReturn(1);

        $plantModel = $this->createMock(PlantModel::class);
        $plantModel->method('getId')->willReturn(100);
        $plantModel->method('getGroup')->willReturn($groupModel);
        $plantModel->method('getAnalytic')->willReturn(null); // Аналитики сначала нет

        $plantService->method('findAll')->willReturn([$plantModel]);

        // 3. Подготовка поливов (UsageModel)
        $usage1 = $this->createMock(UsageModel::class);
        $usage1->method('getUseDate')->willReturn(new \DateTimeImmutable('2024-01-01'));

        $usage2 = $this->createMock(UsageModel::class);
        $usage2->method('getUseDate')->willReturn(new \DateTimeImmutable('2024-01-05')); // Разница 4 дня

        $usageService->method('findBy')->willReturn([$usage1, $usage2]);

        // 4. Ожидания от AnalyticService
        // Ожидаем создание аналитики
        $analyticService->expects($this->once())
            ->method('create')
            ->willReturn($this->createMock(AnalyticModel::class));

        // Ожидаем обновление метрик (1 интервал, среднее 4.0)
        $analyticService->expects($this->once())
            ->method('updateWateringMetrics')
            ->with(100, $this->callback(function ($metrics) {
                return $metrics->getCount() === 1 && $metrics->getAverageDays() === 4.0;
            }));

        // 5. Запуск команды
        $command = new InitWateringStatsCommand(
            $calculator,
            $plantService,
            $usageService,
            $analyticService
        );

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        // 6. Проверки
        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('Success!', $commandTester->getDisplay());
    }
}
