<?php

namespace App\Application\Security;

use App\Domain\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Проверка флага isActive
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('Ваш аккаунт заблокирован.');
        }

        // Если используете SoftDeletable, можно проверить и это
        if ($user->getDeletedAt() !== null) {
            throw new CustomUserMessageAccountStatusException('Аккаунт удален.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Проверки после успешного ввода пароля (например, истечение срока действия пароля)
        if (!$user instanceof User) {
            return;
        }
    }
}
