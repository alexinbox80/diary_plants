<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Repository\PestRepositoryInterface;

class PestRepositoryDecorator implements PestRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly PlantRepositoryDecorator $plantRepository,
        private readonly PestRepository $pestRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PestModel[]
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        $pestsPaginated = $this->pestRepository->getPestsPaginated($page, $perPage);

        if (!is_array($pestsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for pests');
        }

        $pestsModel = array_map(
            fn (Pest $pest): PestModel => $this->toModel($pest, true),
            $pestsPaginated['items']
        );

        return [
            'pestsModel' => $pestsModel,
            'pagination' => $pestsPaginated['pagination']
        ];
    }

    /**
     * @param int $pestId
     * @return Pest|null
     */
    public function find(int $pestId): ?Pest
    {
        return $this->pestRepository->find($pestId);
    }

    /**
     * @param int $pestId
     * @return PestModel|null
     */
    public function findModel(int $pestId): ?PestModel
    {
        $pest = $this->pestRepository->find($pestId);

        return $this->toModel($pest);
    }

    /**
     * @return PestModel[]
     */
    public function findAll(): array
    {
        $pests = $this->pestRepository->findAll();

        return array_map(
            fn (Pest $pest): PestModel => $this->toModel($pest, true),
            $pests
        );
    }

    /**
     * @param string $title
     * @return PestModel[]
     */
    public function findPestsByTitle(string $title): array
    {
        $pests = $this->pestRepository->findPestsByTitle($title);

        return array_map(
            fn (Pest $pest): PestModel => $this->toModel($pest),
            $pests
        );
    }

    /**
     * @param string $manufacturer
     * @return PestModel[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        $pests = $this->pestRepository->findPestsByTitle($manufacturer);

        return array_map(
            fn (Pest $pest): PestModel => $this->toModel($pest),
            $pests
        );
    }

    /**
     * @param DateTimeImmutable $date
     * @return PestModel[]
     */
    public function findPestsByUseDate(DateTimeImmutable $date): array
    {
        $pests = $this->pestRepository->findPestsByUseDate($date);

        return array_map(
            fn (Pest $pest): PestModel => $this->toModel($pest),
            $pests
        );
    }

    /**
     * @param Pest $pest
     * @return int
     */
    public function create(Pest $pest): int
    {
        return $this->pestRepository->create($pest);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->pestRepository->update();
    }

    /**
     * @param Pest $pest
     * @return void
     */
    public function remove(Pest $pest): void
    {
        $this->pestRepository->remove($pest);
    }

    /**
     * @param Pest $pest
     * @param bool $addRelations
     * @return PestModel
     */
    public function toModel(Pest $pest, bool $addRelations = false): PestModel
    {
        $groupModel = null;
        $plantModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->findModel($pest->getGroup()->getId());
            $plantModel = $this->plantRepository->findModel($pest->getPlant()->getId());
        }

        return self::makePestModel($pest, $groupModel, $plantModel);
    }

    /**
     * @param Pest $pest
     * @param GroupModel|null $groupModel
     * @param PlantModel|null $plantModel
     * @return PestModel
     */
    static function makePestModel(Pest $pest, ?GroupModel $groupModel = null, ?PlantModel $plantModel = null): PestModel
    {
        return new PestModel(
            $pest->getId(),
            $pest->getGroup()->getId(),
            $pest->getPlant()->getId(),
            $pest->getTitle(),
            $pest->getVolume()->getQuantity(),
            $pest->getVolume()->getLetter(),
            $pest->getDetails()->getManufacturer(),
            $pest->getDetails()->getDescription(),
            $pest->getDetails()->getComment(),
            $groupModel,
            $plantModel,
            $pest->getCreatedAt(),
            $pest->getUpdatedAt()
        );
    }
}
