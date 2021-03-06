<?php

namespace Modules\Attribute\Repositories;

use Modules\Attribute\Entities\Attribute;

/**
 * 속성관리자
 */
interface AttributesManager
{

    /**
     * Returns all the registered entity
     * @return array
     */
    public function all();

    /**
     * Registers an entity namespace.
     * @param Attribute $entity
     * @return void
     */
    public function registerEntity(Attribute $entity);

    /**
     * 네임스페이스로 검색
     *
     * @param  string $namespace
     * @return mixed
     */
    public function findByNamespace(string $namespace);

    /**
     * 첫번째 엔티티를 리턴
     *
     * @return mixed
     */
    public function first();

}
