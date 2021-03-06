<?php

namespace Modules\Attribute\Repositories\Eloquent;

use Modules\Attribute\Facades\OptionsNormaliser;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentAttributeRepository extends EloquentBaseRepository implements AttributeRepository
{
    public function create($data)
    {
        $data['slug'] = str_slug($data['slug']);

        if(isset($data['options'])) $data['options'] = OptionsNormaliser::normalise(array_get($data, 'options'));

        $attribute = $this->model->create($data);

        $attribute->attributables = array_get($data,'attributables',[]);

        return $attribute;
    }

    public function update($attribute, $data)
    {
        if(isset($data['slug'])) $data['slug'] = str_slug($data['slug']);

        if(isset($data['options'])) $data['options'] = OptionsNormaliser::normalise(array_get($data, 'options'));

        $attribute->update($data);

        $attribute->attributables = array_get($data,'attributables',[]);

        return $attribute;
    }

}
