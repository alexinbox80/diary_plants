<?php

namespace App\Infrastructure\Repository;

use Exception;
use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\Repository\AttachmentRepositoryInterface;
use App\Domain\Model\Interfaces\AttachableModelInterface;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

class AttachmentRepositoryDecorator implements AttachmentRepositoryInterface
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly AttachableResolverInterface $attachableResolver,
        private readonly PlantRepositoryDecorator $plantRepository,
        private readonly OffspringRepositoryDecorator $offspringRepository
    ) {
    }

    /**
     * @return AttachmentModel[]
     */
    public function findByAttachable(string $attachableType, int $attachableId): array
    {
        $attachments = $this->findEntitiesByAttachable($attachableType, $attachableId);

        return array_map(
            fn (Attachment $attachment) => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @return Attachment[]
     */
    public function findEntitiesByAttachable(string $attachableType, int $attachableId): array
    {
        return $this->attachmentRepository->findByAttachable($attachableType, $attachableId);
    }

    /**
     * @return AttachmentModel[]
     */
    public function findByAttachableWithDeleted(string $attachableType, int $attachableId): array
    {
        $attachments = $this->attachmentRepository->findByAttachableWithDeleted($attachableType, $attachableId);

        return array_map(
            fn (Attachment $attachment) => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @param string $attachableType
     * @param int $attachableId
     * @param int $attachmentId
     * @return Attachment|null
     */
    public function findOneByAttachable(string $attachableType, int $attachableId, int $attachmentId): ?Attachment
    {
        return $this->attachmentRepository->findOneByAttachable($attachableType, $attachableId, $attachmentId);
    }

    /**
     * @param string $attachableType
     * @param int $attachableId
     * @param int $attachmentId
     * @return void
     */
    public function deleteByAttachable(string $attachableType, int $attachableId, int $attachmentId): void
    {
        $this->attachmentRepository->deleteByAttachable($attachableType, $attachableId, $attachmentId);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array{attachmentsModel: AttachmentModel[], pagination: array}
     * @throws Exception
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $attachmentsPaginated = $this->attachmentRepository->getAttachmentsPaginated($page, $perPage);

        if (!is_array($attachmentsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for attachments');
        }

        $attachmentsModel = array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment, true),
            $attachmentsPaginated['items']
        );

        return [
            'attachmentsModel' => $attachmentsModel,
            'pagination' => $attachmentsPaginated['pagination']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return AttachmentModel[]
     * @throws Exception
     */
    public function getAttachmentsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $attachmentsPaginated = $this->attachmentRepository->getAttachmentsPaginatedByGroupId($page, $perPage, $groupId);

        if (!is_array($attachmentsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for Attachments');
        }

        $attachmentsModel = array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment, true),
            $attachmentsPaginated['items']
        );

        return [
            'attachmentsModel' => $attachmentsModel,
            'pagination' => $attachmentsPaginated['pagination']
        ];
    }

    /**
     * @param int $attachmentId
     * @return Attachment|null
     */
    public function find(int $attachmentId): ?Attachment
    {
        return $this->attachmentRepository->find($attachmentId);
    }

    /**
     * @param int $attachmentId
     * @return AttachmentModel|null
     */
    public function findModel(int $attachmentId): ?AttachmentModel
    {
        $attachment = $this->attachmentRepository->find($attachmentId);

        return $this->toModel($attachment);
    }

    /**
     * @return AttachmentModel[]
     */
    public function findAll(): array
    {
        $attachments = $this->attachmentRepository->findAll();

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment, true),
            $attachments
        );
    }

    /**
     * @param ?int $groupId
     * @return AttachmentModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $attachments = $this->attachmentRepository->findAllByGroupId($groupId);

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment, true),
            $attachments
        );
    }

    /**
     * @return AttachmentModel[]
     */
    public function findAllWithTargets(): array
    {
        $attachments = $this->attachmentRepository->findAllWithTargets();

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment, true),
            $attachments
        );
    }

    /**
     * @param string $title
     * @return AttachmentModel[]
     */
    public function findAttachmentsByTitle(string $title): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByTitle($title);

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @param string $filename
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFilename(string $filename): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByFilename($filename);

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @param DateTimeImmutable $fileDate
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByFileDate($fileDate);

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @param string $path
     * @return AttachmentModel[]
     */
    public function findAttachmentsByPath(string $path): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByPath($path);

        return array_map(
            fn (Attachment $attachment): AttachmentModel => $this->toModel($attachment),
            $attachments
        );
    }

    /**
     * @param Attachment $attachment
     * @return int
     */
    public function create(Attachment $attachment): int
    {
        return $this->attachmentRepository->create($attachment);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->attachmentRepository->update();
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function remove(Attachment $attachment): void
    {
        $this->attachmentRepository->remove($attachment);
    }

    /**
     * @param Attachment $attachment
     * @param bool $addRelations
     * @return AttachmentModel
     */
    public function toModel(Attachment $attachment, bool $addRelations = false): AttachmentModel
    {
        $groupModel = $this->groupRepository->toModel($attachment->getGroup());

        $attachableModel = null;

        if ($addRelations) {
            $attachableEntity = $this->attachableResolver->resolve(
                $attachment->getTarget()->getAttachableType(),
                $attachment->getTarget()->getAttachableId()
            );

            $attachableModel = $this->toAttachableModel($attachableEntity);
        }

        return AttachmentModel::fromEntity($attachment, $groupModel, $attachableModel);
    }

    /**
     * @param AttachableInterface|null $entity
     * @return AttachableModelInterface|null
     */
    private function toAttachableModel(?AttachableInterface $entity): ?AttachableModelInterface
    {
        if (!$entity) {
            return null;
        }

        $className = get_class($entity);

        return match (AttachableType::fromClass($className)) {
            AttachableType::PLANT => $this->plantRepository->toModel($entity),
            AttachableType::OFFSPRING => $this->offspringRepository->toModel($entity),
            null => null,
        };
    }
}
