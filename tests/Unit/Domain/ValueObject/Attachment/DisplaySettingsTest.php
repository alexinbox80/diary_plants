<?php

namespace Unit\Domain\ValueObject\Attachment;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Attachment\DisplaySettings;

class DisplaySettingsTest extends TestCase
{
    public function testInitializationWithAllParameters(): void
    {
        $settings = new DisplaySettings(
            title: 'Main Title',
            alt: 'Alternative Text',
            description: 'Long description here',
            isShown: false
        );

        $this->assertEquals('Main Title', $settings->getTitle());
        $this->assertEquals('Alternative Text', $settings->getAlt());
        $this->assertEquals('Long description here', $settings->getDescription());
        $this->assertFalse($settings->isShown());
    }

    public function testDefaultValuesInConstructor(): void
    {
        // Передаем пустую строку в alt, так как свойство в классе строго string
        $settings = new DisplaySettings('Only Title', '');

        $this->assertEquals('Only Title', $settings->getTitle());
        $this->assertEquals('', $settings->getAlt());
        $this->assertNull($settings->getDescription());
        $this->assertTrue($settings->isShown());
    }

    public function testVisibilityToggling(): void
    {
        // Добавляем обязательный alt
        $settings = new DisplaySettings('Title', 'Alt', isShown: false);

        $settings->show();
        $this->assertTrue($settings->isShown());

        $settings->hide();
        $this->assertFalse($settings->isShown());
    }

    public function testWithVisibilityCreatesNewInstance(): void
    {
        // Добавляем обязательный alt
        $original = new DisplaySettings('Original', 'Alt', isShown: true);
        $new = $original->withVisibility(false);

        $this->assertNotSame($original, $new);
        $this->assertFalse($new->isShown());
        $this->assertTrue($original->isShown());
        $this->assertEquals($original->getAlt(), $new->getAlt());
    }
}
