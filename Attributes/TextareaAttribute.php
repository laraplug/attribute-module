<?php

namespace Modules\Attribute\Attributes;

use Modules\Attribute\Entities\Attribute;

final class TextareaAttribute extends Attribute
{
    protected $attributes = [
        'type' => 'textarea'
    ];
}
