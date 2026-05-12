<?php

namespace App\Domain\Service;

use App\Domain\Entity\Marker;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Marker\CreateMarkerModel;
use App\Domain\Model\Marker\UpdateMarkerModel;
use App\Domain\Repository\MarkerRepositoryInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Marker\EditMarker\Input\EditMarkerDTO;
use App\Controller\Web\Dashboard\Marker\CreateMarker\Input\CreateMarkerDTO;

class MarkerService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerRepositoryInterface $markerRepository,
        private readonly ModelFactory $modelFactory,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param int|null $groupId
     * @param string|null $type
     * @return array
     */
    public function getChoicesForChoiceType(?int $groupId = null, ?string $type = null): array
    {
        // Получаем список выбора: [letter, description => id]
        $choices = [];
        $markerModels = $this->markerRepository->getMarkersForForm($groupId, $type);

        foreach ($markerModels as $marker) {
            $label = $this->translator->trans('marker.label.format', [
                '%letter%' => $marker->getLetter(),
                '%description%' => $marker->getDescription(),
            ]);

            $choices[$label] = $marker->getId();
        }

        return $choices;
    }

    /**
     * @param int|null $groupId
     * @param string|null $type
     * @return array
     */
    public function getMarkersForDairy(?int $groupId = null, ?string $type = null): array
    {
        return $this->markerRepository->getMarkersForDairy($groupId, $type);
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
     * @return MarkerModel[]
     */
    public function findAll(): array
    {
        return $this->markerRepository->findAll();
    }

    /**
     * @param int|null $groupId
     * @return MarkerModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        return $this->markerRepository->findAllByGroupId($groupId);
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
     * @param int $page
     * @param int $perPage
     * @return MarkerModel[]
     */
    public function getMarkerPaginated(int $page, int $perPage): array
    {
        return $this->markerRepository->getMarkersPaginated($page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return MarkerModel[]
     */
    public function getMarkersPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        return $this->markerRepository->getMarkersPaginatedByGroupId($page, $perPage, $groupId);
    }

    /**
     * @param CreateMarkerModel $createMarkerModel
     * @return MarkerModel
     */
    public function create(CreateMarkerModel $createMarkerModel): MarkerModel
    {
        $group = $this->groupService->find($createMarkerModel->groupId);

        $marker = new Marker(
            $group,
            $createMarkerModel->letter,
            mb_strtoupper($createMarkerModel->color),
            AttachableType::From($createMarkerModel->type),
            $createMarkerModel->description,
            $createMarkerModel->colorDescription
        );

        $this->markerRepository->create($marker);

        return $this->markerRepository->toModel($marker);
    }

    /**
     * @param CreateMarkerDTO $dto
     * @return MarkerModel
     */
    public function createFromCreateMarkerDTO(CreateMarkerDTO $dto): MarkerModel
    {
        $model = $this->modelFactory->makeModel(
            CreateMarkerModel::class,
            $dto->groupId,
            $dto->letter,
            $dto->color,
            $dto->type,
            $dto->description,
            $dto->colorDescription
        );

        return $this->create($model);
    }

    /**
     * @param Marker $marker
     * @param UpdateMarkerModel $updateMarkerModel
     * @return MarkerModel
     */
    public function update(Marker $marker, UpdateMarkerModel $updateMarkerModel): MarkerModel
    {
        $group = $this->groupService->find($updateMarkerModel->groupId);

        $marker
            ->moveToGroup($group)
            ->changeFields(
                $updateMarkerModel->letter,
                mb_strtoupper($updateMarkerModel->color),
                AttachableType::From($updateMarkerModel->type),
                $updateMarkerModel->description,
                $updateMarkerModel->colorDescription
            );

        $this->markerRepository->update();

        return $this->markerRepository->toModel($marker);
    }

    /**
     * @param Marker $marker
     * @param EditMarkerDTO $dto
     * @return MarkerModel
     */
    public function updateFromEditMarkerDTO(Marker $marker, EditMarkerDTO $dto): MarkerModel
    {
        $model = $this->modelFactory->makeModel(
            UpdateMarkerModel::class,
            $dto->groupId,
            $dto->letter,
            $dto->color,
            $dto->type,
            $dto->description,
            $dto->colorDescription
        );

        return $this->update($marker, $model);
    }

    /**
     * @param int $markerId
     * @return void
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
     */
    public function removeMarker(Marker $marker): void
    {
        $this->markerRepository->remove($marker);
    }
}
