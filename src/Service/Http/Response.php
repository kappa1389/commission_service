<?php

namespace App\Service\Http;

class Response
{
    public function __construct(
        protected int $status,
        protected ?string $body = null,
        protected ?string $message = null
    ) {
    }

    public function status(): int
    {
        return $this->status;
    }

    public function body(): ?string
    {
        return $this->body;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }
}
