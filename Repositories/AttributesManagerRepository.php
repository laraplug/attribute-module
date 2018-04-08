<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\Attribute;

/**
 * @inheritDoc
 */
final class AttributesManagerRepository implements AttributesManager
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
     * @inheritDoc
     */
    public function registerEntity(Attribute $entity)
    {
        $this->entities[$entity->type] = $entity;
    }

    /**
     * @inheritDoc
     */
    public function findByNamespace(string $namespace)
    {
        return array_get($this->entities, $namespace, null);
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return collect($this->entities)->first();
    }

}
