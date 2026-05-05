<?php

namespace App\Application\Security;

use LogicException;
use App\Domain\Entity\User;
use App\Domain\ValueObject\Enum\UserRole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessContext
{
    public function __construct(
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Возвращает ID группы для фильтрации данных.
     * null — доступ ко всем группам (для Админа).
     * int — доступ только к конкретной группе.
     */
    public function getTargetGroupId(): ?int
    {
        $user = $this->security->getUser();

        // Проверяем роль через Enum
        $roles = $user->getRoles();
        if (in_array(UserRole::ROLE_ADMIN->value, $roles, true)) {
            return null;
        }

        return $user->getGroup()->getId();
    }

    /**
     * Получает типизированный объект пользователя
     */
    public function getUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            $message = $this->translator->trans('security.access_denied.not_auth');
            throw new LogicException($message);
        }

        return $user;
    }

    /**
     * Получает таймзону текущего пользователя
     */
    public function getTimezone(): string
    {
        return $this->getUser()->getTimeZone();
    }
}
