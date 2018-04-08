<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class Checkbox extends Attribute
{
    /**
     * @var string
     */
    protected $entityNamespace = 'checkbox';

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
