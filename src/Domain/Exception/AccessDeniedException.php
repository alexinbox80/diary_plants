<?php

namespace App\Domain\Exception;

use DomainException;

class AccessDeniedException extends DomainException
{
    public function __construct(string $reason = 'Доступ к данному ресурсу запрещен.')
    {
        parent::__construct($reason);
    }

    /**
     * Можно добавить фабричный метод для конкретных случаев
     */
    public static function forUserRole(string $role): self
    {
        return new self(sprintf('Требуется роль %s для выполнения этого действия.', $role));
    }
}
