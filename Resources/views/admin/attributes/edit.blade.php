@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('attribute::attributes.edit attribute') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.attribute.attribute.index') }}">{{ trans('attribute::attributes.attributes') }}</a></li>
        <li class="active">{{ trans('attribute::attributes.edit attribute') }}</li>
    </ol>
@stop

@section('styles')
    <link href="{!! Module::asset('attribute:css/nestable.css') !!}" rel="stylesheet" type="text/css" />
    <style>
        .options {
            list-style: none;
        }
        .dd-item {
            margin-bottom: 10px;
        }
        .dd-handle {
            display: none;
        }
        .lang-group {
            display: inline;
        }
    </style>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.attribute.attribute.update', $attribute->id], 'method' => 'put', 'ng-app' => 'app']) !!}
    <div class="row">
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers')
                <div class="tab-content">
                    <?php $i = 0; ?>
                    @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                        <?php $i++; ?>
                        <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                            @include('attribute::admin.attributes.partials.edit-fields', ['lang' => $locale])
                        </div>
                    @endforeach
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-body">
                    {!! Form:: normalCheckbox('is_enabled', trans('attribute::attributes.is_enabled'), $errors, $attribute) !!}
                    {!! Form::normalInput('slug', trans('attribute::attributes.slug'), $errors, $attribute, $attribute->is_system?['disabled']:[]) !!}
                    <div class="form-group {{ $errors->has('attributables') ? 'has-error' : '' }}">
                        {!! Form::label('attributables', trans('attribute::attributes.apply to')) !!}
                        {!! Form::select('attributables[]', $attributables, old('attributables', $attribute->attributables->pluck('entity_type')), ['class' => 'selectize', 'multiple', $attribute->is_system?'disabled':'']) !!}
                        {!! $errors->first('attributables', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('attribute::attributes.configuration') }}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                                {!! Form::label('type', trans('attribute::attributes.type')) !!}
                                <select class="form-control jsTypeSelection" name="type" id="type" {{ $attribute->is_system?'disabled':'' }}>
                                    <option value="">{{ trans('attribute::attributes.select a type') }}</option>
                                    @foreach ($types as $entity)
                                        <option data-use-options="{{ $entity->useOptions() }}"
                                                {{ old('type', $attribute->type) === $entity->type ? 'selected' : null }}
                                                value="{{ $entity->type }}">{{  $entity->getTypeName() }}</option>
                                    @endforeach
                                </select>
                                {!! $errors->first('type', '<span class="help-block">:message</span>') !!}
                            </div>

                            <div class="noOptionsArea">
                                {!! Form::normalCheckbox('has_translatable_values', trans('attribute::attributes.has_translatable_values'), $errors, $attribute) !!}
                            </div>

                        </div>
                        <div class="col-md-6" style="margin-top: 25px">
                            @include('attribute::admin.attributes.partials.option-fields')
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                    <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.attribute.attribute.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@section('scripts')
    <script src="{!! Module::asset('menu:js/jquery.nestable.js') !!}"></script>
    <script src="{!! Module::asset('attribute:js/attributes_form.js') !!}"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.attribute.attribute.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Set has_translatable_values to false when type allows option
            $('select[name="type"]').change(function() {
                if($(this).find(':selected').data('use-options') === 1) {
                    $('[name=has_translatable_values]').removeAttr('checked');
                }
            });
            // fire select event to apply current selection
            $('.jsTypeSelection').change();
        });
    </script>
@stop
@include('attribute::admin.attributes.partials.form-angular')
