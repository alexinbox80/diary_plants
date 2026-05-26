<?php

namespace App\Infrastructure\Doctrine\Types;

use InvalidArgumentException;
use Doctrine\DBAL\Types\Type;
use App\Domain\ValueObject\RequestDetails;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class RequestDetailsType extends Type
{
    public const NAME = 'request_details_json';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSONB';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof RequestDetails) {
            throw new InvalidArgumentException('Expected RequestDetails object.');
        }

        return json_encode($value->toArray(), JSON_THROW_ON_ERROR);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?RequestDetails
    {
        if ($value === null || $value === '') {
            return null;
        }

        $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

        return new RequestDetails(
            uri: $data['uri'] ?? '',
            method: $data['method'] ?? '',
            clientIp: $data['client_ip'] ?? null,
            payload: $data['payload'] ?? null
        );
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
