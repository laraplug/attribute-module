<?php

namespace Modules\Attribute\Traits;

use Modules\Attribute\Entities\Attributable;

use Modules\Attribute\Repositories\AttributesManager;

use Illuminate\Support\Facades\Lang;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;

trait AttributableTrait
{

    /**
     * @inheritDoc
     */
    public function getEntityName()
    {
        return get_called_class();
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        $pivotTable = (new Attributable())->getTable();
        return $this->belongsToMany(Attribute::class, $pivotTable, 'entity_type', 'attribute_id', 'type', 'id')
                ->with(['values' => function($query) {
                    $query->where('entity_type', static::class);
                    $query->where('entity_id', static::getKey());
                }]);
    }

    /**
     * {@inheritdoc}
     */
    public function values()
    {
        return $this->morphMany(AttributeValue::class, 'entity');
    }

    /**
     * Get attribute's value (or check if value exists)
     * @param  string  $slug   Attribute Key
     * @param  string|null  $value Value (Use this variable when checking collectioin type value saved)
     * @return array|null
     */
    public function findAttributeValue($slug, $value = null)
    {
        return $this->values()
                    ->when(!is_null($value), function ($query) use ($value) {
                        return $query->where('content', $value);
                    })
                    ->whereHas('attribute', function($query) use ($slug) {
                        $query->where('slug', $slug);
                    })
                    ->first();
    }

    /**
     * Get AttributeValue's content directly (supports translation option)
     * @param  string $slug
     * @param  string $locale
     * @return string
     */
    public function findAttributeValueContent($slug, $locale = null)
    {
        if($attributeValue = $this->findAttributeValue($slug)) {
            if($locale) {
                return $attributeValue->hasTranslation($locale) ? $attributeValue->getTranslation($locale)->content : '';
            }
            return $attributeValue->content;
        }
        return null;
    }

    /**
     * Set Attributes
     * @param array $attributes
     */
    public function setAttributes(array $attributes = [])
    {
        foreach ($attributes as $slug => $contents) {
            if(empty($slug) || empty($contents)) continue;
            // Find attribute by its key
            $attribute = $this->attributes()->where('slug', $slug)->first();
            // If attribute doesn't exist, check if it is dynamicAttribute
            if ($attribute === null) {
                continue;
            }

            // Get every values related to this attribute
            $values = $this->values()->where('attribute_id', $attribute->id);

            // If attribute type is string
            if($attribute->useOptions()) {
                // Treat as array if attribute type is Collection
                if($attribute->isCollection()) {
                    // Apply collection AttributeValue and remove rest of them
                    $appliedIds = array();
                    foreach($contents as $content) {
                        $value = $this->createAttributeValue($attribute, [
                            'content' => $content
                        ]);
                        $appliedIds[] = $value->getKey();
                    }
                    $values->whereNotIn('id', $appliedIds)->delete();
                }
                else {
                    $data = [ 'content' => $contents ];
                    // Apply to first AttributeValue and remove rest of them
                    if($value = $values->first()) {
                        $value->fill($data);
                        $value->save();
                    }
                    else $value = $this->createAttributeValue($attribute, $data);

                    $values->whereNotIn('id', [$value->getKey()])->delete();
                }
            }
            else {
                $data = array();
                // Check translatable
                // (This function should work only for String Type - Input, Textarea)
                if($attribute->has_translatable_values) {
                    // Rearrange structure of Translated Data
                    foreach($contents as $locale => $content) {
                        $data[$locale] = [ 'content' => $content ];
                    }
                }
                else $data = [ 'content' => $contents ];

                // Apply to first AttributeValue and remove rest of them
                if($value = $values->first()) {
                    $value->fill($data);
                    $value->save();
                }
                else $value = $this->createAttributeValue($attribute, $data);

                $values->whereNotIn('id', [$value->getKey()])->delete();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttributes()
    {
        return $this->values()->delete();
    }

    /**
     * Helper method to create AttributeValue
     * @param  Attribute $attribute
     * @param  array  $data
     * @return AttributeValue
     */
    public function createAttributeValue($attribute, array $data)
    {
        $value = new AttributeValue($data);
        $value->attribute_id = $attribute->id;
        $this->values()->save($value);
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTranslatableAttribute()
    {
        return $this->attributes()->where('has_translatable_values', true)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemAttributes()
    {
        return isset($this->systemAttributes) ? collect($this->systemAttributes) : collect([]);
    }

    /**
     * {@inheritdoc}
     */
    public function createSystemAttributes()
    {
        $systemAttributes = $this->getSystemAttributes();
        $systemAttributeIds = [];
        foreach ($systemAttributes as $slug => $config) {
            $config = collect($config);
            if(!$slug || !$config->has('type')) continue;

            $attributeType = app(AttributesManager::class)->findByNamespace($config->get('type'));
            if(!$attributeType) continue;

            $attribute = Attribute::where([
                'type' => $attributeType->type,
                'slug' => $slug
            ])->first();
            // If attributes is not in database
            if($attribute) {
                $attribute->has_translatable_values = $config->get('has_translatable_values', false);
            }
            else {
                // Create Attribute based on system attribustes
                $attributeData = [
                    'type' => $attributeType->type,
                    'slug' => $slug,
                    'has_translatable_values' => $config->get('has_translatable_values', false),
                    'is_enabled' => true,
                    'is_system' => true,
                ];
                foreach (LaravelLocalization::getSupportedLanguagesKeys() as $locale) {
                    $attributeData[$locale]['name'] = $config->has('name') && Lang::has($config->get('name'), $locale) ? trans($config->get('name'), [], $locale) : $slug;
                }
                $attribute = new Attribute($attributeData);
            }

            // Set Options
            if(is_array($config->get('options'))) {
                $optionData = [];
                $options = $attribute->options->keyBy('code');
                foreach ($config->get('options') as $code => $label) {
                    if(is_int($code)) $code = $label;
                    // Don't save if exists
                    if(isset($options[$code])) continue;
                    foreach (LaravelLocalization::getSupportedLanguagesKeys() as $locale) {
                        $optionData[$code][$locale]['label'] = Lang::has($label, $locale) ? trans($label, [], $locale) : $label;
                    }
                }
                $attribute->options = $optionData;
            }
            // Save it to database
            $attribute->save();
            // Attach model to attribute
            $attribute->attributables()->updateOrCreate(['entity_type'=>$this->getEntityNamespace()]);

            $systemAttributeIds[] = $attribute->getKey();
        }
        $this->attributes()->whereNotIn('id', $systemAttributeIds)->update(['is_system'=>false]);
    }

}
