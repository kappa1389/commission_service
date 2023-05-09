<?php

namespace App\Repository\InMemory;

use App\Entity\Entity;
use App\Repository\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var array<int, Entity>
     */
    protected array $entities = [];

    protected int $lastId = 0;

    public function generateId(): int
    {
        $this->lastId = $this->lastId + 1;

        return $this->lastId;
    }

    public function save(Entity $entity): void
    {
        if ($entity->getId() === null) {
            $entity->setId($this->generateId());
        }

        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @param Entity[] $entities
     */
    public function saveMany(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->save($entity);
        }
    }

    public function find(int $id): ?Entity
    {
        return $this->entities[$id] ?? null;
    }

    public function all(): array
    {
        return array_values($this->entities);
    }
}
