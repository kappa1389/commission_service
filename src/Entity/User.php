<?php

namespace App\Entity;

use App\Entity\ValueObject\UserType;

class User extends Entity
{
    public function __construct(protected UserType $type, protected ?int $id = null)
    {
    }

    public static function of(string $type, ?int $id = null): self
    {
        return new self(UserType::from($type), $id);
    }

    public function getType(): UserType
    {
        return $this->type;
    }
}
