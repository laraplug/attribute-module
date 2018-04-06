<?php

namespace Modules\Attribute\Entities;

use Dimsav\Translatable\Translatable;
use Modules\Attribute\Contracts\AttributeInterface;
use Modules\Attribute\Contracts\AttributableInterface;
use Modules\Attribute\Repositories\AttributesManager;
use Illuminate\Database\Eloquent\Model;

/**
 * 속성 모델
 * Attribute Model
 */
class Attribute extends Model implements AttributeInterface
{
    use Translatable;

    protected $table = 'attribute__attributes';
    public $translatedAttributes = ['name', 'description'];
    protected $fillable = [
        'type',
        'slug',
        'has_translatable_values',
        'is_enabled',
        'is_system',
        'options'
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function attributables()
    {
        return $this->hasMany(Attributable::class);
    }

    /**
     * @param $options
     */
    public function setOptionsAttribute($options)
    {
        $inserted_ids = [];
        foreach ($options as $key => $values) {
            if($key) {
                $values['key'] = $key;
                $option = $this->options()->where('key', $key)->first();
                if($option) {
                    $option->fill($values);
                    $option->save();
                }
                else $option = $this->options()->create($values);
                $inserted_ids[] = $option->getKey();
            }
        }

        $this->options()->whereNotIn('id', $inserted_ids)->delete();
    }

    /**
     * @param array $options
     * @return array|mixed
     */
    public function getOptionsAttribute($options)
    {
        return $this->options()->with('translations')->get()->keyBy('key');
    }

    /**
     * @param array $attributables
     */
    public function setAttributablesAttribute($attributables)
    {
        $inserted_ids = [];
        foreach ($attributables as $namespace) {
            $attributable = $this->attributables()->where('entity_type', $namespace)->first();
            if(!$attributable) {
                $attributable = $this->attributables()->create(['entity_type'=>$namespace]);
            }
            $inserted_ids[] = $attributable->getKey();
        }

        $this->attributables()->whereNotIn('id', $inserted_ids)->delete();
    }

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

    /**
     * {@inheritDoc}
     */
    public function getTypeName()
    {
        return trans("attribute::attributes.types.{$this->type}");
    }

    /**
     * {@inheritDoc}
     */
    public function getFormField(AttributableInterface $entity)
    {
        $attribute = $this;
        return view("attribute::admin.types.normal.{$this->type}", compact('attribute', 'entity'));
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslatableFormField(AttributableInterface $entity, $locale)
    {
        $attribute = $this;
        return view("attribute::admin.types.translatable.{$this->type}", compact('attribute', 'entity', 'locale'));
    }


    /**
     * @inheritDoc
     */
    public function getForeignKey()
    {
        return 'attribute_id';
    }

    /**
     * @var string
     */
    protected $translationModel = AttributeTranslation::class;

    // Convert model into specific type (Returns Product if fail)
    public function newFromBuilder($attributes = [], $connection = null)
    {
        // Create Instance
        $manager = app(AttributesManager::class);
        $type = array_get((array) $attributes, 'type');
        $attribute = $type ? $manager->findByNamespace($type) : null;
        $model = $attribute ? $attribute->newInstance([], true) : $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->getConnectionName());
        $model->fireModelEvent('retrieved', false);

        return $model;
    }


}
