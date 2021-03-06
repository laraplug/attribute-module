<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

class InputText extends Attribute
{

    /**
     * @var string
     */
    protected $entityNamespace = 'text';

    /**
     * Check if the current attributes has options
     * @return bool
     */
    public function useOptions()
    {
        return false;
    }

    /**
     * Check if the current attributes has options
     * @return bool
     */
    public function isCollection()
    {
        return false;
    }

}
