<?php

namespace App\Infrastructure\Repository;

use Exception;
use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Entity\Stimulant;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\Repository\StimulantRepositoryInterface;

class StimulantRepositoryDecorator implements StimulantRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly MarkerRepositoryDecorator $markerRepository,
        private readonly StimulantRepository $stimulantRepository,
    ) {
    }

    /**
     * @param int $groupId
     * @return StimulantModel[]
     */
    public function getStimulantsForDairy(int $groupId): array
    {
        $stimulants = $this->stimulantRepository->getStimulantsForDairy($groupId);

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant, true),
            $stimulants
        );
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return StimulantModel[]
     * @throws Exception
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $stimulantsPaginated = $this->stimulantRepository->getStimulantsPaginated($page, $perPage);

        if (!is_array($stimulantsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for stimulants');
        }

        $stimulantsModel = array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant, true),
            $stimulantsPaginated['items']
        );

        return [
            'stimulantsModel' => $stimulantsModel,
            'pagination' => $stimulantsPaginated['pagination']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return StimulantModel[]
     * @throws Exception
     */
    public function getStimulantsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $stimulantsPaginated = $this->stimulantRepository->getStimulantsPaginatedByGroupId($page, $perPage, $groupId);

        if (!is_array($stimulantsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for Stimulants');
        }

        $stimulantsModel = array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant, true),
            $stimulantsPaginated['items']
        );

        return [
            'stimulantsModel' => $stimulantsModel,
            'pagination' => $stimulantsPaginated['pagination']
        ];
    }

    /**
     * @param int $stimulantId
     * @return Stimulant|null
     */
    public function find(int $stimulantId): ?Stimulant
    {
        return $this->stimulantRepository->find($stimulantId);
    }

    /**
     * @param int $stimulantId
     * @return StimulantModel|null
     */
    public function findModel(int $stimulantId): ?StimulantModel
    {
        $stimulant = $this->stimulantRepository->find($stimulantId);

        return $this->toModel($stimulant);
    }

    /**
     * @return StimulantModel[]
     */
    public function findAll(): array
    {
        $stimulants = $this->stimulantRepository->findAll();

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant, true),
            $stimulants
        );
    }

    /**
     * @param ?int $groupId
     * @return StimulantModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $stimulants = $this->stimulantRepository->findAllByGroupId($groupId);

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant, true),
            $stimulants
        );
    }

    /**
     * @param string $title
     * @return StimulantModel[]
     */
    public function findStimulantsByTitle(string $title): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByTitle($title);

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant),
            $stimulants
        );
    }

    /**
     * @param string $manufacturer
     * @return StimulantModel[]
     */
    public function findStimulantsByManufacturer(string $manufacturer): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByTitle($manufacturer);

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant),
            $stimulants
        );
    }

    /**
     * @param DateTimeImmutable $date
     * @return StimulantModel[]
     */
    public function findStimulantsByUseDate(DateTimeImmutable $date): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByUseDate($date);

        return array_map(
            fn (Stimulant $stimulant): StimulantModel => $this->toModel($stimulant),
            $stimulants
        );
    }

    /**
     * @param Stimulant $stimulant
     * @return int
     */
    public function create(Stimulant $stimulant): int
    {
        return $this->stimulantRepository->create($stimulant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->stimulantRepository->update();
    }

    /**
     * @param Stimulant $stimulant
     * @return void
     */
    public function remove(stimulant $stimulant): void
    {
        $this->stimulantRepository->remove($stimulant);
    }

    /**
     * @param Stimulant $stimulant
     * @param bool $addRelations
     * @return StimulantModel
     */
    public function toModel(Stimulant $stimulant, bool $addRelations = false): StimulantModel
    {
        $groupModel = null;
        $markerModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->toModel($stimulant->getGroup());
            $markerModel = $this->markerRepository->toModel($stimulant->getMarker());
        }

        return StimulantModel::fromEntity($stimulant, $groupModel, $markerModel);
    }
}
