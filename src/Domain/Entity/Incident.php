<?php

namespace App\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\RequestDetails;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;

#[ORM\Table(name: 'incident')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'incident__created_at__ind', columns: ['created_at'])]
#[ORM\UniqueConstraint(name: 'incident__public_code__uniq', columns: ['public_code'], options: ['where' => '(deleted_at IS NULL)'])]
final class Incident implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    // идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    // идентификатор пользователя вызвавшего ошибку
    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true)]
    private ?int $userId = null;

    // код ошибки для фронтэнда
    #[ORM\Column(name: 'public_code', length: 64)]
    private string $publicCode;

    // json для хранения информации в бд, ссылка запрос, метод, ip адрес клиента, полезная нагрузка
    #[ORM\Column(name: 'request_detail', type: 'request_details_json')]
    private RequestDetails $requestDetails;

    // HTTP статус-код (например, 500)
    #[ORM\Column(name: 'status_code', type: Types::INTEGER)]
    private int $statusCode;

    // Текст системной ошибки / Exception message
    #[ORM\Column(name: 'error_message', type: 'text')]
    private string $errorMessage;

    // Используем для сохранения полного пути вызова функций (стека вызовов), приведшего к ошибке
    #[ORM\Column(name: 'stack_trace', type: Types::JSON)]
    private array $stackTrace;

    public function __construct(
        RequestDetails $requestDetails,
        int $statusCode,
        string $errorMessage,
        array $stackTrace,
        ?int $userId = null
    ) {
        $this->publicCode = 'ERR-' . substr(Uuid::v4()->toBase58(), 0, 8);
        $this->requestDetails = $requestDetails;
        $this->statusCode = $statusCode;
        $this->errorMessage = $errorMessage;
        $this->stackTrace = $stackTrace;
        $this->userId = $userId;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getPublicCode(): string
    {
        return $this->publicCode;
    }

    public function getRequestDetails(): RequestDetails
    {
        return $this->requestDetails;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getStackTrace(): array
    {
        return $this->stackTrace;
    }
}
