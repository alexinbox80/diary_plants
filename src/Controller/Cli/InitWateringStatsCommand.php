<?php

namespace App\Controller\Cli;

use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Service\AnalyticService;
use App\Domain\Service\AnalyticsCalculator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Domain\Model\Analytic\CreateAnalyticModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use Symfony\Component\Console\Output\OutputInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[AsCommand(
    name: 'app:stats:init-watering',
    description: 'Initializes the average watering interval statistics based on Usage history.',
)]
class InitWateringStatsCommand extends Command
{
    public function __construct(
        private readonly AnalyticsCalculator $calculator,
        private readonly PlantService $plantService,
        private readonly UsageService $usageService,
        private readonly AnalyticService $analyticService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('plantId', InputArgument::OPTIONAL, 'ID of a specific plant (if you only need to count one)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $plantId = $input->getArgument('plantId');

        if ($plantId !== null) {
            // Проверяем, что передано именно число
            if (!is_numeric($plantId)) {
                $io->error(sprintf('Plant ID "%s" is not a valid number.', $plantId));
                return Command::FAILURE;
            }

            $plantModel = $this->plantService->findModel((int)$plantId);

            if (!$plantModel) {
                $io->error(sprintf('Plant with ID %d not found.', $plantId));
                return Command::FAILURE;
            }

            $plantModels = [$plantModel];
        } else {
            $plantModels = $this->plantService->findAll();
        }

        if (empty($plantModels)) {
            $io->warning('There are no plants to process.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($plantModels));

        foreach ($plantModels as $plantModel) {
            // 1. Убеждаемся, что у растения есть сущность аналитики
            if (!$plantModel->getAnalytic()) {
                $this->analyticService->create(
                    new CreateAnalyticModel(
                        $plantModel->getId(),
                        $plantModel->getGroup()->getId()
                    )
                );
            }

            // 2. Получаем все даты поливов
            $waterings = $this->usageService->findBy($plantModel, AttachableType::WATERING->value);
            $dates = array_map(fn(UsageModel $usage) => $usage->getUseDate(), $waterings);

            // 3. Делегируем расчет калькулятору
            $metrics = $this->calculator->calculateFullMetrics($dates);

            // 4. Обновляем метрики
            $this->analyticService->updateWateringMetrics(
                $plantModel->getId(),
                $metrics
            );

            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success('Success!');

        return Command::SUCCESS;
    }
}
