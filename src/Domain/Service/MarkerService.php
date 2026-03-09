<?php

namespace App\Domain\Service;

use App\Domain\Entity\Marker;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Marker\CreateMarkerModel;
use App\Domain\Model\Marker\UpdateMarkerModel;
use App\Domain\Repository\MarkerRepositoryInterface;

class MarkerService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerRepositoryInterface $markerRepository
    ) {
    }

    /**
     * @param int $markerId
     * @return ?Marker
     */
    public function find(int $markerId): ?Marker
    {
        return $this->markerRepository->find($markerId);
    }

    /**
     * @return Marker[]
     */
    public function findAll(): array
    {
        return $this->markerRepository->findAll();
    }

    /**
     * @param string $letter
     * @return MarkerModel[]
     */
    public function findMarkersByLetter(string $letter): array
    {
        return $this->markerRepository->findMarkersByLetter($letter);
    }

    /**
     * @param string $color
     * @return MarkerModel[]
     */
    public function findMarkersByColor(string $color): array
    {
        return $this->markerRepository->findMarkersByColor($color);
    }


    /**
     * @return MarkerModel[]
     * @throws InvalidArgumentException
     */
    public function getMarkerPaginated(int $page, int $perPage): array
    {
        return $this->markerRepository->getMarkersPaginated($page, $perPage);
    }

    /**
     * @param CreateMarkerModel $createMarkerModel
     * @return MarkerModel
     * @throws InvalidArgumentException
     */
    public function create(CreateMarkerModel $createMarkerModel): MarkerModel
    {
        $group = $this->groupService->find($createMarkerModel->groupId);

        $marker = new Marker(
            $group,
            $createMarkerModel->letter,
            $createMarkerModel->color,
            $createMarkerModel->description,
            $createMarkerModel->colorDescription
        );

        $this->markerRepository->create($marker);

        return $this->markerRepository->toModel($marker);
    }

    /**
     * @param Marker $marker
     * @param UpdateMarkerModel $updateMarkerModel
     * @return MarkerModel
     * @throws InvalidArgumentException
     */
    public function update(Marker $marker, UpdateMarkerModel $updateMarkerModel): MarkerModel
    {
        $group = $this->groupService->find($updateMarkerModel->groupId);

        $marker->changeFields(
            $group,
            $updateMarkerModel->letter,
            $updateMarkerModel->color,
            $updateMarkerModel->description,
            $updateMarkerModel->colorDescription
        );

        $this->markerRepository->update();

        return $this->markerRepository->toModel($marker);
    }

    /**
     * @param int $markerId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $markerId): void
    {
        $marker = $this->markerRepository->find($markerId);
        if ($marker !== null) {
            $this->markerRepository->remove($marker);
        }
    }

    /**
     * @param Marker $marker
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeMarker(Marker $marker): void
    {
        $this->markerRepository->remove($marker);
    }
}
