<div class="box-body">
    {!! Form::i18nInput('name', trans('attribute::attributes.name'), $errors, $lang, $attribute) !!}
    {!! Form::i18nTextarea('description', trans('attribute::attributes.description'), $errors, $lang, $attribute) !!}
</div>
