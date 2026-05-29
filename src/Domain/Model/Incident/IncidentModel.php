<?php

namespace App\Domain\Model\Incident;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Incident;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\ValueObject\RequestDetails;

final class IncidentModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $publicCode,
        private readonly RequestDetails $requestDetails,
        private readonly int $statusCode,
        private readonly string $errorMessage,
        private readonly array $stackTrace,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private readonly ?int $userId = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param Incident $incident
     * @return IncidentModel
     */
    public static function fromEntity(Incident $incident): self
    {
        return new self(
            $incident->getId(),
            $incident->getPublicCode(),
            $incident->getRequestDetails(),
            $incident->getStatusCode(),
            $incident->getErrorMessage(),
            $incident->getStackTrace(),
            $incident->getCreatedAt(),
            $incident->getUpdatedAt(),
            $incident->getUserId()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.incident.header.id',
            'user_id' => 'table.incident.header.user_id',
            'public_code' => 'table.incident.header.public_code',
            'uri' => 'table.incident.header.uri',
            'method' => 'table.incident.header.method',
            'client_ip' => 'table.incident.header.client_ip',
            'payload' => 'table.incident.header.payload',
            'status_code' => 'table.incident.header.status_code',
            'error_message' => 'table.incident.header.error_message',
            'stack_trace' => 'table.incident.header.stack_trace',
            'created_at' => 'table.incident.header.created_at',
            'updated_at' => 'table.incident.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'public_code' => $this->getPublicCode(),
            'uri' => $this->getRequestDetails()->getUri(),
            'method' => $this->getRequestDetails()->getMethod(),
            'client_ip' => $this->getRequestDetails()->getClientIp(),
            'payload' => $this->getRequestDetails()->getPayload(),
            'status_code' => $this->getStatusCode(),
            'error_message' => $this->getErrorMessage(),
            'stack_trace' => $this->getStackTrace(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
