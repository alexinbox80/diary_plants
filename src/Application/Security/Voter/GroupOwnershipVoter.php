<?php

namespace App\Application\Security\Voter;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Enum\UserRole;
use App\Domain\Entity\Interfaces\GroupOwnedInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class GroupOwnershipVoter extends Voter
{
    public const VIEW = 'ENTITY_VIEW';
    public const EDIT = 'ENTITY_EDIT';
    public const DELETE = 'ENTITY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Проверяем, что атрибут наш и объект реализует интерфейс
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof GroupOwnedInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) return false;

        if (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles(), true)) {
            return true;
        }

        /** @var GroupOwnedInterface $subject */
        return $user->getGroup()->getId() === $subject->getGroupId();
    }
}
