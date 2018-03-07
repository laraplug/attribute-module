<?php

namespace Modules\Attribute\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

final class AttributeValue extends Model
{
    use Translatable;

    protected $table = 'attribute__attribute_values';
    public $translatedAttributes = [];
    protected $fillable = [
        'content'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    // Optional Relation if value type is collection
    public function getOption($key)
    {
        return $this->belongsTo(AttributeOption::class, 'attribute_id', 'attribute_id')->where('key', $key)->first();
    }

    /**
     * @param string $content
     * @return array|mixed
     */
    public function getContentAttribute($content)
    {
        if($this->attribute->isCollection() && $option = $this->getOption($content)) {
            return $option->label;
        }
        else if($this->attribute->has_translatable_values) {
            return $this->translations->content;
        }
        return $content;
    }
}
