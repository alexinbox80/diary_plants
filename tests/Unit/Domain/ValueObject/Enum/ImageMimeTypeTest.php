<?php

namespace Unit\Domain\ValueObject\Enum;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\ImageMimeType;

class ImageMimeTypeTest extends TestCase
{
    public function testEnumValuesMatchStandardMimeTypes(): void
    {
        $this->assertEquals('image/jpeg', ImageMimeType::JPEG->value);
        $this->assertEquals('image/png', ImageMimeType::PNG->value);
        $this->assertEquals('image/gif', ImageMimeType::GIF->value);
        $this->assertEquals('image/webp', ImageMimeType::WEBP->value);
    }

    public function testGetValuesReturnsAllSupportedMimes(): void
    {
        $expected = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        $this->assertEquals($expected, ImageMimeType::getValues());
        $this->assertCount(4, ImageMimeType::getValues());
    }

    public function testIsValidReturnsTrueForSupportedMimes(): void
    {
        $this->assertTrue(ImageMimeType::isValid('image/jpeg'));
        $this->assertTrue(ImageMimeType::isValid('image/webp'));
    }

    public function testIsValidReturnsFalseForUnsupportedMimes(): void
    {
        $this->assertFalse(ImageMimeType::isValid('image/svg+xml'));
        $this->assertFalse(ImageMimeType::isValid('application/pdf'));
        $this->assertFalse(ImageMimeType::isValid('text/plain'));
    }

    public function testTryFromReturnsEnumOrNull(): void
    {
        $this->assertSame(ImageMimeType::PNG, ImageMimeType::tryFrom('image/png'));
        $this->assertNull(ImageMimeType::tryFrom('invalid/mime'));
    }
}
