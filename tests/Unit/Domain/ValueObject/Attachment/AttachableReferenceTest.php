<?php

namespace Unit\Domain\ValueObject\Attachment;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Attachment\AttachableReference;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

class AttachableReferenceTest extends TestCase
{
    public function testInitialization(): void
    {
        $id = 42;
        $type = AttachableType::PLANT;

        $reference = new AttachableReference($id, $type);

        $this->assertEquals($id, $reference->getAttachableId());
        $this->assertEquals($type, $reference->getAttachableType());
    }

    public function testIsPlantReturnsTrueForPlantType(): void
    {
        $reference = new AttachableReference(1, AttachableType::PLANT);

        $this->assertTrue($reference->isPlant());
        $this->assertFalse($reference->isOffspring());
    }

    public function testIsOffspringReturnsTrueForOffspringType(): void
    {
        $reference = new AttachableReference(1, AttachableType::OFFSPRING);

        $this->assertTrue($reference->isOffspring());
        $this->assertFalse($reference->isPlant());
    }

    public function testNullableValues(): void
    {
        $reference = new AttachableReference(null, null);

        $this->assertNull($reference->getAttachableId());
        $this->assertNull($reference->getAttachableType());
        $this->assertFalse($reference->isPlant());
        $this->assertFalse($reference->isOffspring());
    }
}
