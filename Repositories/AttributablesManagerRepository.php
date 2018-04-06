<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Contracts\AttributableInterface;

/**
 * @inheritDoc
 */
final class AttributablesManagerRepository implements AttributablesManager
{
    /**
     * @var array
     */
    private $entities = [];

    public function all()
    {
        return $this->entities;
    }

    /**
     * Registers an entity namespace.
     * @param AttributableInterface $entity
     * @return void
     */
    public function registerEntity(AttributableInterface $entity)
    {
        $this->entities[$entity->getEntityNamespace()] = $entity;
    }

    /**
     * @inheritDoc
     */
    public function findByNamespace(string $namespace)
    {
        return array_get($this->entities, $namespace, null);
    }

}
