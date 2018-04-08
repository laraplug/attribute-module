<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class MultiSelect extends Attribute
{
    /**
     * @var string
     */
    protected $entityNamespace = 'multiselect';

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
