<?php

namespace App\Services;

class ServiceResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public int $statusCode = 200,
        public ?\Throwable $exception = null
    ) {}

    public static function success(mixed $data = null, string $message = null, int $statusCode = 200): self
    {
        return new self(true, $data, $message, $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, ?\Throwable $exception = null): self
    {
        return new self(false, null, $message, $statusCode, $exception);
    }
}
