<?php

namespace Unit\Domain\ValueObject\Repotting;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class RepottingDetailsTest extends TestCase
{
    public function testInitialization(): void
    {
        $type = RepottingType::POTTING_UP;
        $material = PotMaterial::CERAMIC;
        $size = '14 см';

        $details = new RepottingDetails($type, $material, $size);

        $this->assertSame($type, $details->getType());
        $this->assertSame($material, $details->getMaterial());
        $this->assertEquals($size, $details->getPotSize());
    }

    public function testGettersReturnCorrectEnumCases(): void
    {
        $details = new RepottingDetails(
            RepottingType::EMERGENCY,
            PotMaterial::PLASTIC,
            '10'
        );

        $this->assertInstanceOf(RepottingType::class, $details->getType());
        $this->assertSame(RepottingType::EMERGENCY, $details->getType());

        $this->assertInstanceOf(PotMaterial::class, $details->getMaterial());
        $this->assertSame(PotMaterial::PLASTIC, $details->getMaterial());
    }
}
