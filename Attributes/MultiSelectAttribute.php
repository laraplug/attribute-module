<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class MultiSelectAttribute extends Attribute
{
    protected $attributes = [
        'type' => 'multiselect'
    ];

    /**
     * @inheritDoc
     */
    public function useOptions()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isCollection()
    {
        return true;
    }
}
