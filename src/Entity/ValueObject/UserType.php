<?php

namespace App\Entity\ValueObject;

enum UserType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';

    public function isPrivate(): bool
    {
        return $this === self::PRIVATE;
    }

    public function isBusiness(): bool
    {
        return $this === self::BUSINESS;
    }
}
