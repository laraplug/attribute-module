<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class SelectAttribute extends Attribute
{
    protected $attributes = [
        'type' => 'select'
    ];

    /**
     * @inheritDoc
     */
    public function useOptions()
    {
        return true;
    }

}
