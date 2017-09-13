@if(old(convert_input_to_dot_separated_array($name)))
    @php($defaultImage = old(convert_input_to_dot_separated_array($name)))
@endif
<div class="@if ($errors->has(convert_input_to_dot_separated_array($name))) has-error @endif">
    <div class="fileinput" data-id="{{uniqid('file-manager-')}}">
        <div class="fileinput-new thumbnail">
            <div class="options">
                <ul>
                    <li class="remove-red tooltips remove-image" data-toggle="tooltip" title="Remove" data-placement="bottom">
                        <i class="fa fa-times"></i>
                    </li>
                    <li class="tooltips open-file" data-toggle="tooltip" title="Open" data-file="{!! isset($defaultImage) ?  url('uploads/original/'.$defaultImage) : url("assets/admin/images/placeholder.png") !!}" data-placement="bottom">
                        <i class="fa fa-external-link" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips select-file" data-toggle="tooltip" title="Choose" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips upload-file" data-toggle="tooltip" title="Upload" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </li>
                    @if($croppable)
                        <li class="tooltips crop-image" data-toggle="tooltip" title="Crop" @if(isset($defaultImage)) data-image-path="{{$defaultImage}}" @endif data-placement="bottom" @if(isset($cropName)) data-crop-name="{{$cropName}}" @endif data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>
                            <i class="fa fa-crop" aria-hidden="true"></i>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="img-container"></div>
            <img src="{!! isset($defaultImage) ?  get_file_thumbnail($defaultImage): url("assets/admin/images/placeholder.png") !!}" class="image-input-placeholder">
            @if($croppable)
                <p style="font-size:12px;" class="help-block">Min: {{get_crop_min_sizes($cropName)}}</p>
            @endif
            <input type="hidden" name="{{$name}}" class="file-name-input" value="{{isset($defaultImage) ? $defaultImage : ''}}">
            @if(isset($fields))
                <div class="input-fields-container">
                    {!! $fields !!}
                </div>
            @endif
        </div>
    </div>
        @if ($errors->has(convert_input_to_dot_separated_array($name)))
            <p class="help-block">{{ $errors->first(convert_input_to_dot_separated_array($name)) }}</p>
        @endif
</div>