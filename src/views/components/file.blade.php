@if(old($name))
    @php($defaultImage = old($name))
@endif
<div class="@if ($errors->has($name)) has-error @endif">
    <br />
    <div class="fileinput" data-id="{{uniqid('file-manager-')}}">
        <div class="fileinput-new thumbnail">
            <div class="options">
                <ul>
                    <li class="remove-red tooltips remove-image" data-toggle="tooltip" title="הסר" data-placement="bottom">
                        <i class="fa fa-times"></i>
                    </li>
                    <li class="tooltips open-file" data-toggle="tooltip" title="פתח" data-file="{!! isset($defaultFile) ?  url('uploads/original/'.$defaultFile) : url("assets/admin/images/placeholder.png") !!}" data-placement="bottom">
                        <i class="fa fa-external-link" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips select-file" data-toggle="tooltip" title="בחר" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips upload-file" data-toggle="tooltip" title="העלה" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </li>
                </ul>
            </div>
            <div class="img-container"></div>
            <img src="{!! isset($defaultImage) ?  get_file_thumbnail($defaultImage): url("assets/admin/images/placeholder.png") !!}" class="image-input-placeholder">
            <input type="hidden" name="{{$name}}" class="file-name-input" value="{{isset($defaultImage) ? $defaultImage : ''}}">
            @if(isset($fields))
                <div class="input-fields-container">
                    {!! $fields !!}
                </div>
            @endif
        </div>
    </div>
    @if ($errors->has($name))
        <p class="help-block">{{ $errors->first($name) }}</p>
    @endif
</div>