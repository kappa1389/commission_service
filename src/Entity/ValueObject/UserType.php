<?php

namespace App\Entity\ValueObject;

enum UserType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';
}
