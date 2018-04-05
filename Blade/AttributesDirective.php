<?php

namespace Modules\Attribute\Blade;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Attribute\Contracts\AttributableInterface;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Repositories\AttributablesManager;

final class AttributesDirective
{
    /**
     * @var string
     */
    private $attributable;
    /**
     * @var AttributableInterface
     */
    private $entity;
    /**
     * @var AttributeRepository
     */
    private $attribute;
    /**
     * @var AttributablesManager
     */
    private $attributablesManager;

    public function __construct(AttributeRepository $attribute, AttributablesManager $attributablesManager)
    {
        $this->attribute = $attribute;
        $this->attributablesManager = $attributablesManager;
    }

    public function show($arguments)
    {
        $this->extractArguments($arguments);

        $this->entity->createSystemAttributes();

        $attributable = $this->attributablesManager->findByNamespace($this->attributable);

        $attributes = $attributable->attributes()->get();

        return $this->renderForm($this->entity, $attributes);
    }

    /**
     * Extract the possible arguments as class properties
     * @param array $arguments
     */
    private function extractArguments(array $arguments)
    {
        $this->attributable = array_get($arguments, 0);
        $this->entity = array_get($arguments, 1);
    }

    private function renderForm(AttributableInterface $entity, $attributes = [], $view = null)
    {
        $attributable = $this->attributable;
        $view = $view ?: 'attribute::admin.blade.form';

        $form = '';
        $translatableForm = '';

        $normalAttributes = $attributes->where('has_translatable_values', false);
        $translatableAttributes = $attributes->where('has_translatable_values', true);

        foreach ($normalAttributes as $attribute) {
            $form .= $attribute->getFormField($entity);
        }

        foreach ($translatableAttributes as $attribute) {
            foreach(LaravelLocalization::getSupportedLanguagesKeys() as $i => $locale) {
                $active = locale() == $locale ? 'active' : '';
                $translatableForm .= "<div class='tab-pane $active' id='tab_attributes_$i'>";
                $translatableForm .= $attribute->getTranslatableFormField($entity, $locale);
                $translatableForm .= '</div>';
            }
        }

        return view($view, compact('entity', 'form', 'translatableForm'));
    }
}
