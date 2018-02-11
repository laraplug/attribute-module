<?php

namespace Modules\Attribute\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

final class UpdateAttributeRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
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
        ];
    }

    public function translationMessages()
    {
        return [
            'name.required' => trans('attribute::attributes.name is required'),
        ];
    }
}
