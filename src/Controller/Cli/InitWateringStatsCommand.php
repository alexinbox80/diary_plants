<?php

namespace App\Controller\Cli;

use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
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

        if ($plantId) {
            $plantModel = $this->plantService->findModel((int)$plantId);
            $plantModels = $plantModel ? [$plantModel] : [];
        } else {
            $plantModels = $this->plantService->findAll();
        }

        if (empty($plantModels)) {
            $io->warning('There are no plants to process.');
            return Command::SUCCESS;
        }

        $io->progressStart(count($plantModels));

        foreach ($plantModels as $plantModel) {
            $analyticModel = $plantModel->getAnalytic();

            // Если аналитики нет — создаем её через сервис
            if (!$analyticModel) {
                // Создаем DTO для создания
                $createModel = new CreateAnalyticModel(
                    $plantModel->getId(),
                    $plantModel->getGroup()->getId()
                );

                $analyticModel = $this->analyticService->create($createModel);
            }

            $waterings = $this->usageService->findBy($plantModel, AttachableType::WATERING->value);

            // Инициализируем значения для расчета
            $count = 0;
            $average = 0.0;

            if (count($waterings) >= 2) {
                $lastDate = null;
                foreach ($waterings as $usage) {
                    $currentDate = $usage->getUseDate();
                    if ($lastDate !== null) {
                        $diff = $currentDate->diff($lastDate);
                        $daysPassed = (float) $diff->days;
                        $count++;
                        $average = $this->calculator->calculateNewAverage($average, $count, $daysPassed);
                    }
                    $lastDate = $currentDate;
                }
            }

            // Обновляем метрики через сервис (он внутри найдет Entity и сделает flush)
            $this->analyticService->updateWateringMetrics(
                $plantModel->getId(),
                new IntervalMetrics($count, $average)
            );

            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success('Success!');

        return Command::SUCCESS;
    }
}
