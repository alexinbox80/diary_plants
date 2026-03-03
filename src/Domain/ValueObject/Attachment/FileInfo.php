<?php //метаданные файла

namespace App\Domain\ValueObject\Attachment;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class FileInfo
{
    //имя файла
    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: true)]
    private ?string $filename = null;

    //путь к файлу
    #[ORM\Column(name: 'path', type: 'string', length: 255, nullable: true)]
    private ?string $path = null;

    //тип майм файла
    #[ORM\Column(name: 'mime_type', type: 'string', length: 50, nullable: true)]
    private ?string $mimeType = null;

    //дата загрузки файла
    #[ORM\Column(name: 'file_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $fileDate = null;

    public function __construct(
        ?string $filename = null,
        ?string $path = null,
        ?string $mimeType = null,
        ?DateTimeImmutable $fileDate = null
    ) {
        $this->filename = $filename;
        $this->path = $path;
        $this->mimeType = $mimeType;
        $this->fileDate = $fileDate;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getFileDate(): ?DateTimeImmutable
    {
        return $this->fileDate;
    }
}
