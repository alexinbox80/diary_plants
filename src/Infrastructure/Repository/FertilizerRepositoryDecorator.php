<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\Repository\FertilizerRepositoryInterface;

class FertilizerRepositoryDecorator implements FertilizerRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator  $groupRepository,
        private readonly MarkerRepositoryDecorator $markerRepository,
        private readonly FertilizerRepository      $fertilizerRepository
    ) {
    }

    /**
     * @return FertilizerModel[]
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $fertilizersPaginated = $this->fertilizerRepository->getFertilizersPaginated($page, $perPage);

        if (!is_array($fertilizersPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for fertilizers');
        }

        $fertilizersModel = array_map(
            fn (Fertilizer $fertilizer): FertilizerModel => $this->toModel($fertilizer, true),
            $fertilizersPaginated['items']
        );

        return [
            'fertilizersModel' => $fertilizersModel,
            'pagination' => $fertilizersPaginated['pagination']
        ];
    }

    /**
     * @param int $fertilizerId
     * @return Fertilizer|null
     */
    public function find(int $fertilizerId): ?Fertilizer
    {
        return $this->fertilizerRepository->find($fertilizerId);
    }

    /**
     * @param int $fertilizerId
     * @return FertilizerModel|null
     */
    public function findModel(int $fertilizerId): ?FertilizerModel
    {
        $fertilizer = $this->fertilizerRepository->find($fertilizerId);

        return $this->toModel($fertilizer);
    }

    /**
     * @return FertilizerModel[]
     */
    public function findAll(): array
    {
        $fertilizers = $this->fertilizerRepository->findAll();

        return array_map(
            fn (Fertilizer $fertilizer): FertilizerModel => $this->toModel($fertilizer, true),
            $fertilizers
        );
    }

    /**
     * @param string $title
     * @return FertilizerModel[]
     */
    public function findFertilizersByTitle(string $title): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByTitle($title);

        return array_map(
            fn (Fertilizer $fertilizer): FertilizerModel => $this->toModel($fertilizer),
            $fertilizers
        );
    }

    /**
     * @param string $manufacturer
     * @return FertilizerModel[]
     */
    public function findFertilizersByManufacturer(string $manufacturer): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByTitle($manufacturer);

        return array_map(
            fn (Fertilizer $fertilizer): FertilizerModel => $this->toModel($fertilizer),
            $fertilizers
        );
    }

    /**
     * @param DateTimeImmutable $date
     * @return FertilizerModel[]
     */
    public function findFertilizersByUseDate(DateTimeImmutable $date): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByUseDate($date);

        return array_map(
            fn (Fertilizer $fertilizer): FertilizerModel => $this->toModel($fertilizer),
            $fertilizers
        );
    }

    /**
     * @param Fertilizer $fertilizer
     * @return int
     */
    public function create(Fertilizer $fertilizer): int
    {
        return $this->fertilizerRepository->create($fertilizer);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->fertilizerRepository->update();
    }

    /**
     * @param Fertilizer $fertilizer
     * @return void
     */
    public function remove(Fertilizer $fertilizer): void
    {
        $this->fertilizerRepository->remove($fertilizer);
    }

    /**
     * @param Fertilizer $fertilizer
     * @param bool $addRelations
     * @return FertilizerModel
     */
    public function toModel(Fertilizer $fertilizer, bool $addRelations = false): FertilizerModel
    {
        $groupModel = null;
        $markerModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->toModel($fertilizer->getGroup());
            $markerModel = $this->markerRepository->toModel($fertilizer->getMarker());
        }

        return FertilizerModel::fromEvent($fertilizer, $groupModel, $markerModel);
    }
}
