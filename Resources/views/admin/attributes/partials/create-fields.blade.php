<div class="box-body">
    {!! Form::i18nInput('name', trans('attribute::attributes.name'), $errors, $lang, null) !!}
    {!! Form::i18nTextarea('description', trans('attribute::attributes.description'), $errors, $lang, null) !!}
</div>
