<?php

namespace App\Infrastructure\Storage;

use RuntimeException;
use InvalidArgumentException;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class LocalFileStorage
{
    private string $uploadDirectory;

    public function __construct(
        string $uploadDirectory,
        private readonly BuilderInterface $customQrCodeBuilder
    ) {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param string $directory
     * @return array
     */
    public function getFilesInDirectory(string $directory): array
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException("Directory does not exist: {$directory}");
        }

        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            $path = $directory . '/' . $item;

            // Exclude '.' и '..'
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (is_file($path)) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $directory
     * @param bool $moveFlag
     * @return File
     */
    public function storeUploadedFile(UploadedFile $uploadedFile, string $directory, bool $moveFlag = true): File
    {
        $directory = $this->uploadDirectory . '/' . $directory;

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf(
                    'Failed to create directory: %s. Check permissions and the existence of the parent directory.',
                    $directory
                ));
            }
        }

        $fileName = sprintf('%s.%s', uniqid('image', true), $uploadedFile->getClientOriginalExtension());

        if ($moveFlag) {
            return $uploadedFile->move($directory, $fileName);
        } else {
            //copy($uploadedFile->getRealPath(), $directory . '/' . $fileName);
            copy($uploadedFile->getPathname(), $directory . '/' . $fileName);

            return new File($directory . '/' . $fileName);
        }

    }

    /**
     * @param string $path
     * @return bool
     */
    public function removeUploadedFile(string $path): bool
    {
        $file = $this->uploadDirectory . '/' . $path;

        if (file_exists($file)) {
            if (unlink($file)) {
                return true;
            } else {
                throw new RuntimeException(sprintf(
                    'Failed to delete file: %s. Check permissions and file existence.',
                    $file
                ));
            }
        } else {
            throw new RuntimeException(sprintf(
                'Failed to delete file: %s.',
                $file
            ));
        }
    }

    /**
     * @param string $path
     * @param string $uuid
     * @param string $url
     * @return string
     */
    public function createQrCodeFile(string $path, string $uuid, string $url): string
    {
        $directory = $this->uploadDirectory . '/' . $path;

        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new RuntimeException(sprintf(
                    'Failed to create directory: %s. Check permissions and the existence of the parent directory.',
                    $directory
                ));
            }
        }

        $fileName = $uuid . '.png';
        $fullPath = $directory . $fileName;

        // Используем билдер пакета
        $result = $this->customQrCodeBuilder->build(
            writer: new PngWriter(),
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
        );

        // Сохраняем файл
        $result->saveToFile($fullPath);

        return $path . $fileName;
    }

    /**
     * Перемещает файл или директорию из одного места в другое внутри хранилища
     *
     * @param string $oldRelativePath Относительный путь (например, 'qr-code/1/10/uuid.png')
     * @param string $newRelativePath Новый относительный путь (например, 'qr-code/2/10/uuid.png')
     * @return string Возвращает новый относительный путь
     */
    public function move(string $oldRelativePath, string $newRelativePath): string
    {
        $filesystem = new Filesystem();

        $oldFullPath = $this->uploadDirectory . '/' . ltrim($oldRelativePath, '/');
        $newFullPath = $this->uploadDirectory . '/' . ltrim($newRelativePath, '/');

        if (!$filesystem->exists($oldFullPath)) {
            throw new RuntimeException(sprintf('Source path does not exist: %s', $oldFullPath));
        }

        try {
            // 1. Создаем целевую директорию (mkdir -p)
            // dirname($newFullPath) автоматически вычислит путь к папке
            $filesystem->mkdir(dirname($newFullPath));

            // 2. Перемещаем файл или директорию
            // rename() в Symfony Filesystem работает атомарно и заменяет существующий файл, если нужно
            $filesystem->rename($oldFullPath, $newFullPath, true);

            // 3. Удаляем старую директорию, если она пуста
            $oldDirectory = dirname($oldFullPath);

            // Важный нюанс: если вы переместили ПОСЛЕДНИЙ файл из папки,
            // имеет смысл удалить пустую папку, чтобы не плодить мусор.
            $this->removeEmptyDirectoriesUp($oldDirectory);

        } catch (IOExceptionInterface $exception) {
            throw new RuntimeException(sprintf('Error occurred while moving file: %s', $exception->getMessage()));
        }

        return $newRelativePath;
    }

    /**
     * Рекурсивно удаляет пустые директории вверх по дереву
     * до базовой директории загрузок.
     *
     * @param string $dir
     * @return void
     */
    private function removeEmptyDirectoriesUp(string $dir): void
    {
        $filesystem = new Filesystem();

        // Очищаем путь от лишних слешей и приводим к каноничному виду
        $dir = realpath($dir);
        $baseDir = realpath($this->uploadDirectory);

        // Условие: пока путь является директорией, он пуст и мы не вышли за пределы базовой папки
        while (
            $dir &&
            $dir !== $baseDir &&
            is_dir($dir) &&
            $this->isDirEmpty($dir)
        ) {
            $filesystem->remove($dir);

            // Поднимаемся на уровень выше
            $dir = dirname($dir);
        }
    }

    /**
     * @param string $dir
     * @return bool
     */
    private function isDirEmpty(string $dir): bool
    {
        if (!is_dir($dir)) return false;
        return (count(scandir($dir)) <= 2); // . и ..
    }
}
