<?php

namespace App\Repository;

use App\Entity\Entity;

interface RepositoryInterface
{
    public function save(Entity $entity): void;

    /**
     * @return Entity[]
     */
    public function all(): array;

    /**
     * @param Entity[] $entities
     */
    public function saveMany(array $entities): void;
}
