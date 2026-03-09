<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Marker;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Repository\MarkerRepositoryInterface;

class MarkerRepositoryDecorator implements MarkerRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly MarkerRepository $markerRepository,
    ) {
    }

    /**
     * @return MarkerModel[]
     */
    public function getMarkersPaginated(int $page, int $perPage): array
    {
        $markersPaginated = $this->markerRepository->getMarkersPaginated($page, $perPage);

        if (!is_array($markersPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for Markers');
        }

        $markersModel = array_map(
            fn (Marker $marker): MarkerModel => $this->toModel($marker, true),
            $markersPaginated['items']
        );

        return [
            'markersModel' => $markersModel,
            'pagination' => $markersPaginated['pagination']
        ];
    }

    /**
     * @param int $markerId
     * @return Marker|null
     */
    public function find(int $markerId): ?Marker
    {
        return $this->markerRepository->find($markerId);
    }

    /**
     * @param int $markerId
     * @return MarkerModel|null
     */
    public function findModel(int $markerId): ?MarkerModel
    {
        $marker = $this->markerRepository->find($markerId);

        return $this->toModel($marker);
    }

    /**
     * @return markerModel[]
     */
    public function findAll(): array
    {
        $markers = $this->markerRepository->findAll();

        return array_map(
            fn (Marker $marker): MarkerModel => $this->toModel($marker, true),
            $markers
        );
    }

    /**
     * @param string $letter
     * @return MarkerModel[]
     */
    public function findMarkersByLetter(string $letter): array
    {
        $markers = $this->markerRepository->findMarkersByLetter($letter);

        return array_map(
            fn (Marker $marker): MarkerModel => $this->toModel($marker),
            $markers
        );
    }

    /**
     * @param string $color
     * @return MarkerModel[]
     */
    public function findMarkersByColor(string $color): array
    {
        $markers = $this->markerRepository->findMarkersByColor($color);

        return array_map(
            fn (Marker $marker): MarkerModel => $this->toModel($marker),
            $markers
        );
    }

    /**
     * @param Marker $marker
     * @return int
     */
    public function create(Marker $marker): int
    {
        return $this->markerRepository->create($marker);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->markerRepository->update();
    }

    /**
     * @param Marker $marker
     * @return void
     */
    public function remove(Marker $marker): void
    {
        $this->markerRepository->remove($marker);
    }

    /**
     * @param Marker $marker
     * @param bool $addRelations
     * @return MarkerModel
     */
    public function toModel(Marker $marker, bool $addRelations = false): MarkerModel
    {
        $groupModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->findModel($marker->getGroup()->getId());
        }

        return self::makeMarkerModel($marker, $groupModel);
    }

    /**
     * @param Marker $marker
     * @param GroupModel|null $groupModel
     * @return MarkerModel
     */
    static function makeMarkerModel(Marker $marker, ?GroupModel $groupModel = null): MarkerModel
    {
        return new MarkerModel(
            $marker->getId(),
            $marker->getGroup()->getId(),
            $marker->getLetter(),
            $marker->getColor(),
            $marker->getDescription(),
            $marker->getColorDescription(),
            $groupModel,
            $marker->getCreatedAt(),
            $marker->getUpdatedAt()
        );
    }
}
