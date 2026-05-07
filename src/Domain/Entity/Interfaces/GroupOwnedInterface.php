<?php

namespace App\Domain\Entity\Interfaces;

use App\Domain\Entity\Group;

interface GroupOwnedInterface
{
    public function getGroup(): Group;

    public function getGroupId(): int;

    public function moveToGroup(Group $group): self;
}
