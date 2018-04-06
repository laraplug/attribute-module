<?php

namespace Modules\Attribute\Entities;

use Modules\Attribute\Contracts\AttributableInterface;
use Modules\Attribute\Repositories\AttributablesManager;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class Attributable extends Pivot
{

    protected $table = 'attribute__attributables';
    protected $fillable = [
        'entity_type'
    ];
    public $timestamps = false;

    /**
     * 속성대상 모델 가져오기
     * Get Attributable Entity
     * @return AttributableInterface
     */
    public function getEntityAttribute()
    {
        return app(AttributablesManager::class)->findByNamespace($this->entity_type);
    }

    /**
     * 속성대상 이름 가져오기
     * Get Attributable Entity Name
     * @return AttributableInterface
     */
    public function getNameAttribute()
    {
        return $this->entity ? $this->entity->getEntityName() : '';
    }

}
