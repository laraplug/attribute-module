<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Contracts\AttributableInterface;

/**
 * 속성대상 관리자
 */
interface AttributablesManager
{
    
    /**
     * Returns all the registered entity.
     * @return array
     */
    public function all();

    /**
     * Registers an entity namespace.
     * @param AttributableInterface $entity
     * @return void
     */
    public function registerEntity(AttributableInterface $entity);

    /**
     * 네임스페이스로 검색
     *
     * @param  string $namespace
     * @return mixed
     */
    public function findByNamespace(string $namespace);

}
