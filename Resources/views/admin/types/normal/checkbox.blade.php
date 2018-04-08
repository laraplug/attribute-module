<div class="form-group {{ $errors->has('attributes.' . $attribute->slug) ? 'has-error' : '' }}">
    {!! Form::label($attribute->name, $attribute->name) !!}

    <?php foreach ($attribute->options as $option): ?>
    <label class="checkbox">
        <input type="checkbox" name="attributes[{{ $attribute->slug }}][]"
                class="flat-blue"
                data-slug="{{ $attribute->slug }}"
                data-is-collection="{{ $attribute->isCollection() }}"
                value="{{ $option->code }}" {{ $entity->findAttributeValue($attribute->slug, $option->code) ? 'checked' : '' }}>
        {{ $option->getLabel() }}
    </label>
    <?php endforeach; ?>
    {!! $errors->first('attributes.' . $attribute->slug, '<span class="help-block">:message</span>') !!}
</div>
