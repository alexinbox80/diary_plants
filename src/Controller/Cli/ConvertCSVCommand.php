<?php

namespace App\Controller\Cli;

use App\Domain\Service\Csv\CsvService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::CONVERT_CSV_COMMAND_NAME, hidden: false)]
final class ConvertCSVCommand extends Command
{
    public const CONVERT_CSV_COMMAND_NAME = 'database:convert:csv';
    public const CONVERT_CSV_DESCRIPTION = 'Convert CSV files to corresponding entities';

    public function __construct(
        private readonly string $csvFilePrefix,
        private readonly CsvService $csvService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(self::CONVERT_CSV_DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = 0;
        $output->write(sprintf("<info> Run command %s </info>\n", self::CONVERT_CSV_COMMAND_NAME ));

        // Get all entries (files and directories)
        $allEntries = scandir($this->csvFilePrefix);

        // Filter out '.' (current directory) and '..' (parent directory)
        $files = array_diff($allEntries, array('.', '..'));

        // Print the list of files
        foreach ($files as $file) {
            $count = $this->csvService->process($this->csvFilePrefix . $file);

            $output->write(sprintf("<info> From file %s %d records were created</info>\n", $file, $count));
        }

        return self::SUCCESS;
    }
}
