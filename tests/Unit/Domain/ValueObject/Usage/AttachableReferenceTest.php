<?php

namespace Unit\Domain\ValueObject\Usage;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\ValueObject\Usage\AttachableReference;

class AttachableReferenceTest extends TestCase
{
    public function testInitialization(): void
    {
        $id = 123;
        $type = AttachableType::FERTILIZER;

        $reference = new AttachableReference($id, $type);

        $this->assertEquals($id, $reference->getUsableId());
        $this->assertSame($type, $reference->getUsableType());
    }

    public function testIsMethodsReturnCorrectBoolean(): void
    {
        $reference = new AttachableReference(1, AttachableType::WATERING);

        $this->assertTrue($reference->isWatering());
        $this->assertFalse($reference->isFertilizer());
        $this->assertFalse($reference->isPest());
        $this->assertFalse($reference->isStimulant());
    }

    public function testPestIdentification(): void
    {
        $reference = new AttachableReference(1, AttachableType::PEST);
        $this->assertTrue($reference->isPest());
    }

    public function testStimulantIdentification(): void
    {
        $reference = new AttachableReference(1, AttachableType::STIMULANT);
        $this->assertTrue($reference->isStimulant());
    }

    public function testNullableInitialization(): void
    {
        $reference = new AttachableReference(null, null);

        $this->assertNull($reference->getUsableId());
        $this->assertNull($reference->getUsableType());
        $this->assertFalse($reference->isWatering());
    }
}
