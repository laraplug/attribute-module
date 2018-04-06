<?php

namespace Modules\Attribute\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

use Modules\Attribute\Entities\Attribute;

final class CreateAttributeRequest extends BaseFormRequest
{
    public function rules()
    {
        $attributeTable = (new Attribute())->getTable();
        return [
            'attributables' => 'required|array',
            'slug' => 'required|unique:'.$attributeTable.',slug',
            'type' => 'required',
        ];
    }

    public function translationRules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'slug.unique' => trans('attribute::attributes.slug already exists'),
        ];
    }

    public function translationMessages()
    {
        return [
            'name.required' => trans('attribute::attributes.name is required'),
        ];
    }
}
