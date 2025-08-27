<?php

namespace App\Controller\Cli;

//use App\Domain\Service\FollowerService;
//use App\Domain\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: self::CONVERT_CSV_COMMAND_NAME, description: self::CONVERT_CSV_DESCRIPTION, hidden: true)]
final class ConvertCSVCommand extends Command
{
    public const CONVERT_CSV_COMMAND_NAME = 'database:convert:csv';
    public const CONVERT_CSV_DESCRIPTION = 'Convert CSV files to corresponding entities';

//    private const DEFAULT_FOLLOWERS = 10;
//    private const DEFAULT_LOGIN_PREFIX = 'Reader #';
//
    public function __construct(
        private readonly string $csvFilePrefix,
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

    /**
     * @param string $filePath
     * @return \Generator
     * @throws \Exception
     */
    public static function convertCsv(string $filePath): \Generator
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new \Exception();
        }

        //fgetcsv($handle, separator: ';');
        // пока не достигнем конца файла
        while (!feof($handle)) {
            // читаем строку
            // и генерируем значение
            yield fgetcsv($handle, separator: ';');
        }

        // закрываем
        fclose($handle);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write("<info> Run command " . self::CONVERT_CSV_COMMAND_NAME . " </info>\n");

        $localPath =  $this->csvFilePrefix . 'plant.csv';

        $array = [];
        foreach (self::convertCsv($localPath) as $row) {
            if (!empty($row)) {
                foreach ($row as $key => $item) {
                    dump($key, $item);
//                    if (($key > 0) && ($item != '') && ($flagId === true)) {
//                        if ($fillableColumns[$key - 1] === 'birth_date') {
//                            $item = Carbon::createFromFormat('d.m.Y', $item)->format('Y-m-d');
//                        }
//                        $array[$fillableColumns[$key - 1]] = $item;
//                    }
//
//                    if (!empty($item) && ($flagId === false)) {
//                        if ($fillableColumns[$key] === 'date') {
//                            $item = Carbon::createFromFormat('d.m.Y', $item)->format('Y-m-d');
//                        }
//                        $array[$fillableColumns[$key]] = $item;
//                    }
                }
                //$model::create($array);
            }
            $array = [];
        }



//        $authors = (int)$input->getArgument('authors');
//        $count = (int)$input->getArgument('count');
//
//        $faker = Faker::create();
//
//        for ($i = 0; $i < $authors; $i++) {
//            $authorModel = new CreateAuthorModel(
//                $faker->firstName,
//                $faker->lastName,
//                $faker->text(rand(100, 200))
//            );
//
//            $result = $this->authorService->create($authorModel);
//
//            for ($j = 0; $j < $count; $j += 1000) {
//                dump($j);
//                $books = $this->generateData($i, $j, 1000, $faker);
//
//                foreach ($books as $book) {
//                    $bookModel = new CreateBookModel(
//                        $book['authorId'],
//                        $book['title'],
//                        $book['description'],
//                    );
//                    $this->bookService->create($bookModel);
//                }
//            }
//
//
//
////            for ($j = 0; $j < $count; $j++) {
////                $bookModel = new CreateBookModel(
////                    $i + 1,
////                    $faker->words(rand(2, 5), true),
////                    $faker->text(rand(100, 200))
////                );
////                $this->bookService->create($bookModel);
////            }
//        }

        //$output->write("<info> " . $authors * $count . " records were created</info>\n");

        return self::SUCCESS;
    }
}
