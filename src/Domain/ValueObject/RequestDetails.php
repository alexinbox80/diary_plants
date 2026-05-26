<?php //Иммутабельный объект-значение для хранения деталей HTTP-запроса.

namespace App\Domain\ValueObject;

final class RequestDetails
{
    public function __construct(
        // ссылка запрос
        public string $uri,

        // метод
        public string $method,

        // ip адрес клиента
        public ?string $clientIp,

        // полезная нагрузка
        public ?string $payload
    ) {
        // Обрезаем payload, если он гигантский, чтобы не перегружать БД
        if ($this->payload && strlen($this->payload) > 5000) {
            $this->payload = substr($this->payload, 0, 5000) . '... [TRUNCATED]';
        }
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getClientIp(): ?string
    {
        return $this->clientIp;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function toArray(): array
    {
        return [
            'uri' => $this->uri,
            'method' => $this->method,
            'client_ip' => $this->clientIp,
            'payload' => $this->payload,
        ];
    }
}
