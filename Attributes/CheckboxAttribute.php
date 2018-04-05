<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class CheckboxAttribute extends Attribute
{
    protected $attributes = [
        'type' => 'checkbox'
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
