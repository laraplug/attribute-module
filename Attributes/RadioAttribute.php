<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class RadioAttribute extends Attribute
{
    protected $attributes = [
        'type' => 'radio'
    ];

    /**
     * @inheritDoc
     */
    public function useOptions()
    {
        return true;
    }

}
