<li class="dd-item">
    <div class="form-inline">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon dd-handle"><i class="fa fa-arrows"></i></div>
                <input class="form-control" name="options[{{$count}}][value]"
                       type="text" value="{{ isset($value) ? $value : '' }}" placeholder="{{ trans('attribute::attributes.option value') }}" {{ isset($option) ? 'readonly' : '' }}>
            </div>
            <?php foreach (LaravelLocalization::getSupportedLocales() as $locale => $language): ?>
            <div class="lang-group {{ $locale }}" style="{{ $locale !== locale() ? 'display: none;' : ''}}">
                <input class="form-control" id="label" name="options[{{$count}}][{{$locale}}][label]"
                       type="text" value="{{ $option[$locale]['label'] }}" placeholder="{{ trans('attribute::attributes.option label') }}">
            </div>
            <?php endforeach; ?>
            <select name="" class="form-control jsOptionLanguage">
                <?php foreach (LaravelLocalization::getSupportedLocales() as $locale => $language): ?>
                <option value="{{ $locale }}">{{ $language['name'] }}</option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-default jsAddRow"><i class="fa fa-plus"></i></button>
            <button class="btn btn-default jsRemoveRow"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
</li>
