<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class Select extends Attribute
{
    /**
     * @var string
     */
    protected $entityNamespace = 'select';

    /**
     * @inheritDoc
     */
    public function useOptions()
    {
        return true;
    }

}
