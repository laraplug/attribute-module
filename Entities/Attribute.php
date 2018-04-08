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
     * @param array $options
     */
    public function setOptionsAttribute($options)
    {
        static::saved(function ($model) use ($options) {
            $savedIds = [];
            foreach ($options as $code => $data) {
                if (empty(array_filter($data))) {
                    continue;
                }
                if(empty($data['code'])) $data['code'] = $code;
                // Create option or enable it if exists
                $option = $this->options()->updateOrCreate([
                    'code' => $data['code']
                ], $data);
                $savedIds[] = $option->id;
            }
            
            if(!empty($savedIds)) {
                $this->options()->whereNotIn('id', $savedIds)->delete();
            }
        });
    }

    /**
     * @param array $options
     * @return array|mixed
     */
    public function getOptionsAttribute()
    {
        return $this->options()->with('translations')->get();
    }

    /**
     * @param array $attributables
     */
    public function setAttributablesAttribute($attributables)
    {
        static::saved(function ($model) use ($options) {
            $savedIds = [];
            foreach ($attributables as $namespace) {
                $attributable = $this->attributables()->where('entity_type', $namespace)->first();
                if(!$attributable) {
                    $attributable = $this->attributables()->create(['entity_type'=>$namespace]);
                }
                $savedIds[] = $attributable->getKey();
            }
            $this->attributables()->whereNotIn('id', $savedIds)->delete();
        });
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
     * @var string
     */
    protected $entityNamespace = '';

    /**
     * {@inheritDoc}
     */
    public function getTypeAttribute()
    {
        return $this->entityNamespace;
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
