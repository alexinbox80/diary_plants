<?php

namespace App\Domain\Exception;

use DomainException;

/**
 * Доменное исключение: используется, когда бизнес-логика не может найти пользователя.
 */
class UserNotFoundException extends DomainException
{
    public function __construct(int $id)
    {
        // Формируем понятное сообщение.
        // Оно будет доступно через $exception->getMessage()
        parent::__construct(sprintf('Пользователь с ID "%d" не найден.', $id));
    }
}
