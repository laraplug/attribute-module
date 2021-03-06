<div class="form-group {{ $errors->has('attributes.' . $attribute->slug) ? 'has-error' : '' }}">
    {!! Form::label($attribute->name, $attribute->name) !!}

    <select name="attributes[{{ $attribute->slug }}]"
        class="form-control jsOptionLanguage"
        data-slug="{{ $attribute->slug }}"
        data-is-collection="{{ $attribute->isCollection() }}">
        <?php foreach ($attribute->options as $option): ?>
        <option value="{{ $option->code }}" {{ $entity->findAttributeValue($attribute->slug, $option->code) ? 'selected' : '' }}>{{ $option->getLabel() }}</option>
        <?php endforeach; ?>
    </select>

    {!! $errors->first('attributes.' . $attribute->slug, '<span class="help-block">:message</span>') !!}
</div>
