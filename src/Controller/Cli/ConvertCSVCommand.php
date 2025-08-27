<?php

namespace App\Controller\Cli;

use App\Domain\Service\CsvService;
use App\Domain\Service\PlantService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::CONVERT_CSV_COMMAND_NAME, description: self::CONVERT_CSV_DESCRIPTION, hidden: true)]
final class ConvertCSVCommand extends Command
{
    public const CONVERT_CSV_COMMAND_NAME = 'database:convert:csv';
    public const CONVERT_CSV_DESCRIPTION = 'Convert CSV files to corresponding entities';

    public function __construct(
        private readonly string $csvFilePrefix,
        private readonly CsvService $csvService,
        private readonly PlantService $plantService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(self::CONVERT_CSV_COMMAND_NAME)
            ->setDescription(self::CONVERT_CSV_DESCRIPTION);
    }

    private function generateData(int $authorId, int $start, int $count, $faker): \Generator
    {
        for ($i = $start; $i <= $count + $start; $i++) {
            yield [
                'authorId' => $authorId + 1,
                'title' => $faker->words(rand(2, 5), true),
                'description' => $faker->text(rand(100, 200))
            ];
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = 0;

        $output->write("<info> Run command " . self::CONVERT_CSV_COMMAND_NAME . " </info>\n");

        $localPath =  $this->csvFilePrefix . 'plant.csv';

        $count += $this->csvService->process($this->plantService, $localPath);

        $output->write("<info> " . $count . " records were created</info>\n");

        return self::SUCCESS;
    }
}
