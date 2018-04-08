<?php

namespace Modules\Attribute\Contracts;

interface AttributeInterface
{

    /**
     * Returns a key for the type.
     * @return string
     */
    public function getTypeAttribute();

    /**
     * Returns a human friendly name for the type.
     * @return string
     */
    public function getTypeName();

    /**
     * Returns the HTML for creating / editing an entity.
     * @param Attribute $attribute
     * @param AttributableInterface $entity
     * @return string
     */
    public function getFormField(AttributableInterface $entity);

    /**
     * Returns the HTML for creating / editing an entity that has translatable values.
     * @param Attribute $attribute
     * @param AttributableInterface $entity
     * @param string $locale
     * @return string
     */
    public function getTranslatableFormField(AttributableInterface $entity, $locale);

    /**
     * Returns boolean for whether the type allows to use options.
     * @return bool
     */
    public function useOptions();

    /**
     * Returns boolean for whether the type is collection.
     * @return bool
     */
    public function isCollection();
}
