<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class Radio extends Attribute
{
    /**
     * @var string
     */
    protected $entityNamespace = 'radio';

    /**
     * @inheritDoc
     */
    public function useOptions()
    {
        return true;
    }

}
