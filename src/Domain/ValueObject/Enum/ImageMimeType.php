<?php

namespace App\Domain\ValueObject\Enum;

enum ImageMimeType: string
{
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case WEBP = 'image/webp';

    /**
     * Получить все допустимые MIME-типы как массив
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Проверить, является ли переданный MIME-тип допустимым
     *
     * @param string $mimeType
     * @return bool
     */
    public static function isValid(string $mimeType): bool
    {
        return in_array($mimeType, self::getValues(), true);
    }
}
