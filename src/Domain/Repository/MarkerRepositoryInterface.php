<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Marker;
use App\Domain\Model\Marker\MarkerModel;

interface MarkerRepositoryInterface
{
    public function getMarkersPaginated(int $page, int $perPage): array;
    public function getMarkersForForm(?int $groupId = null, ?string $type = null): array;
    public function getMarkersForDairy(?int $groupId = null, ?string $type = null): array;
    public function find(int $markerId): ?Marker;
    public function findModel(int $markerId): ?MarkerModel;
    public function findAll(): array;
    public function findMarkersByLetter(string $letter): array;
    public function findMarkersByColor(string $color): array;
    public function create(Marker $marker): int;
    public function update(): void;
    public function remove(Marker $marker): void;
    public function toModel(Marker $marker, bool $addRelations = false): MarkerModel;
}
