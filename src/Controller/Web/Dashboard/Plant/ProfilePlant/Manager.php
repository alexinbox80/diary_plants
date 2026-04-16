<?php

namespace App\Controller\Web\Dashboard\Plant\ProfilePlant;

use App\Domain\ValueObject\OId;
use App\Domain\Service\PlantService;
use App\Domain\Model\Plant\PlantModel;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getPlantProfile(string $uuid): array
    {
        $oid = Oid::fromString($uuid);

        $plantModel = $this->plantService->findPlantByUUID($oid);
        $profileHeader = PlantModel::getProfileHeaderRu();

        $profileBody = $plantModel->profileToArray();

        return [
            'profileHeader' => $profileHeader,
            'profileBody' => $profileBody
        ];
    }
}

