<?php

namespace App\Tests\Controller\Cli;

use App\Controller\Cli\ConvertCSVCommand;
use App\Domain\Service\CsvService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ConvertCSVCommandTest extends TestCase
{
    public function testCommandIsRegistered(): void
    {
        $command = new ConvertCSVCommand(
            csvFilePrefix: '/path/to/data/',
            csvService: $this->createMock(CsvService::class),
            plantService: $this->createMock(PlantService::class)
        );

        $this->assertSame('database:convert:csv', $command->getName());
        $this->assertSame('Convert CSV files to corresponding entities', $command->getDescription());
    }

    public function testCommandExecution(): void
    {
        // Мокаем ModelFactory
        $mockModelFactory = $this->getMockBuilder(ModelFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Мокаем PlantService
        $mockPlantService = $this->getMockBuilder(PlantService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        // Мокаем CsvService
        $mockCsvService = $this->getMockBuilder(CsvService::class)
            ->setConstructorArgs([
                ';', // csvSeparator
                $mockModelFactory,
                $mockPlantService,
            ])
            ->onlyMethods(['process'])
            ->getMock();

        $mockCsvService->expects($this->once())
            ->method('process')
            ->with($this->equalTo('/path/to/data/test.csv'))
            ->willReturn(5);

        // Создаём команду
        $command = new ConvertCSVCommand(
            csvFilePrefix: '/path/to/data/',
            csvService: $mockCsvService,
            plantService: $mockPlantService
        );

        $input = new ArrayInput([]);

        $output = new NullOutput();

        $result = $command->run($input, $output);

        $this->assertSame(0, $result);
    }
}
