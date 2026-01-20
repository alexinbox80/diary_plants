<?php

namespace App\Domain\Service;

use App\Domain\Entity\Pest;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Model\Pest\UpdatePestModel;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Repository\PestRepositoryInterface;
use DateTimeImmutable;
use Psr\Cache\InvalidArgumentException;

class PestService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly PestRepositoryInterface $pestRepository
    ) {
    }

    /**
     * @param int $pestId
     * @return ?Pest
     */
    public function find(int $pestId): ?Pest
    {
        return $this->pestRepository->find($pestId);
    }

    /**
     * @return Pest[]
     */
    public function findAll(): array
    {
        return $this->pestRepository->findAll();
    }

    /**
     * @param string $title
     * @return PestModel[]
     */
    public function findPestsByTitle(string $title): array
    {
        return $this->pestRepository->findPestsByTitle($title);
    }

    /**
     * @param string $manufacturer
     * @return PestModel[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        return $this->pestRepository->findPestsByManufacturer($manufacturer);
    }

    /**
     * @param DateTimeImmutable $date
     * @return PestModel[]
     */
    public function findPestsByUseDate(DateTimeImmutable $date): array
    {
        return $this->pestRepository->findPestsByUseDate($date);
    }

    /**
     * @return PestModel[]
     * @throws InvalidArgumentException
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        return $this->pestRepository->getPestsPaginated($page, $perPage);
    }

    /**
     * @param CreatePestModel $createPestModel
     * @return PestModel
     * @throws InvalidArgumentException
     */
    public function create(CreatePestModel $createPestModel): PestModel
    {
        $plant = $this->plantService->find($createPestModel->plantId);

        $pest = new Pest(
            $plant,
            $createPestModel->title,
            $createPestModel->quantity,
            $createPestModel->letter,
            $createPestModel->manufacturer,
            $createPestModel->description,
            $createPestModel->comment
        );

        $this->pestRepository->create($pest);

        return new PestModel(
            $pest->getId(),
            $pest->getPlant()->getId(),
            $pest->getTitle(),
            $pest->getQuantity(),
            $pest->getLetter(),
            $pest->getManufacturer(),
            $pest->getDescription(),
            $pest->getComment(),
            $pest->getCreatedAt(),
            $pest->getUpdatedAt()
        );
    }

    /**
     * @param Pest $pest
     * @param UpdatePestModel $updatePestModel
     * @return PestModel
     * @throws InvalidArgumentException
     */
    public function update(Pest $pest, UpdatePestModel $updatePestModel): PestModel
    {
        $plant = $this->plantService->find($updatePestModel->plantId);

        $pest->changeFieldsWithPlant(
            $updatePestModel->title,
            $updatePestModel->quantity,
            $updatePestModel->letter,
            $plant,
            $updatePestModel->manufacturer,
            $updatePestModel->description,
            $updatePestModel->comment
        );

        $this->pestRepository->update();

        return  new PestModel(
            $pest->getId(),
            $pest->getPlant()->getId(),
            $pest->getTitle(),
            $pest->getQuantity(),
            $pest->getLetter(),
            $pest->getManufacturer(),
            $pest->getDescription(),
            $pest->getComment(),
            $pest->getCreatedAt(),
            $pest->getUpdatedAt()
    );
    }

    /**
     * @param int $pestId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $pestId): void
    {
        $pest = $this->pestRepository->find($pestId);
        if ($pest !== null) {
            $this->pestRepository->remove($pest);
        }
    }

    /**
     * @param Pest $pest
     * @return void
     * @throws InvalidArgumentException
     */
    public function removePest(Pest $pest): void
    {
        $this->pestRepository->remove($pest);
    }
}
