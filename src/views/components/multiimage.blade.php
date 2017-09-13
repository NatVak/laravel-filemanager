<div class="file-multi-container" data-id="{{uniqid('file-manager-container-')}}">
    <div class="file-multi-options">
        <button class="upload-files" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"true"' data-fileType='"image"' data-newMinFiles='"{{(isset($minFiles) && $minFiles) ? $minFiles : 1}}"' data-newMaxFiles='"{{((isset($maxFiles) && $maxFiles) ? $maxFiles : 99999)}}"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>
            Upload
        </button>
        <button class="select-files" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"true"' data-fileType='"image"' data-newMinFiles='"{{(isset($minFiles) && $minFiles) ? $minFiles : 1}}' data-newMaxFiles='"{{((isset($maxFiles) && $maxFiles) ? $maxFiles : 99999)}}"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>
            Choose
        </button>
        <button class="red-button remove-files">Clear all</button>
        @if($croppable)
            <p style="font-size:12px;" class="help-block">Min: {{get_crop_min_sizes($cropName)}}</p>
        @endif
    </div>
    <div class="files-containers">

        @if(get_old_inputs_as_array($name))
            @php($defaultImages = get_old_inputs_as_array($name))
        @endif

        @if($defaultImages)
            @foreach($defaultImages as $k => $defaultImage)
                <div class="fileinput @if ($errors->has($name)) has-error @endif ui-state-default multi" data-id="{{uniqid('file-manager-')}}">
                    <div class="fileinput-new thumbnail">
                        <div class="options">
                            <ul>
                                <li class="remove-red tooltips remove-image-multi" data-toggle="tooltip" title="Remove" data-placement="bottom">
                                    <i class="fa fa-times"></i>
                                </li>
                                <li class="tooltips open-file" data-toggle="tooltip" title="Open" data-file="{{url('uploads/original/'.$defaultImage)}}" data-placement="bottom">
                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                </li>
                                <li class="tooltips select-file" data-toggle="tooltip" title="Choose" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                                    <i class="fa fa-cloud" aria-hidden="true"></i>
                                </li>
                                <li class="tooltips upload-file" data-toggle="tooltip" title="Upload" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                </li>
                                @if($croppable)
                                    <li class="tooltips crop-image" data-toggle="tooltip" title="Crop" data-image-path="{{$defaultImage}}"  data-placement="bottom" @if(isset($cropName)) data-crop-name="{{$cropName}}" @endif data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>
                                        <i class="fa fa-crop" aria-hidden="true"></i>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <img src="{{get_file_thumbnail($defaultImage)}}" class="image-input-placeholder">
                        <input type="hidden" name="{{$name}}" class="file-name-input" value="{{$defaultImage}}">
                        @if(isset($fields[$k]))
                            <div class="input-fields-container">
                                {!! $fields[$k] !!}
                            </div>
                        @elseif(isset($clonerField))
                            {!! $clonerField !!}
                        @endif
                        @if (count(array_get(get_array_error_messages($errors, $name), $k.'.0', [])))
                            <p class="help-block">{{ array_get(get_array_error_messages($errors, $name), $k.'.0') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<script type="html/template" id="clone_item">
    <div class="fileinput" data-id="__UNIQUE_ID__">
        <div class="fileinput-new thumbnail multi">
            <div class="options">
                <ul>
                    <li class="remove-red tooltips remove-image-multi" data-toggle="tooltip" title="הסר" data-placement="bottom">
                        <i class="fa fa-times"></i>
                    </li>
                    <li class="tooltips open-file" data-toggle="tooltip" title="פתח" data-file="__FILE_PATH__" data-placement="bottom">
                        <i class="fa fa-external-link" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips select-file" data-toggle="tooltip" title="בחר" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips upload-file" data-toggle="tooltip" title="העלה" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"image"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </li>
                    @if($croppable)
                        <li class="tooltips crop-image" data-toggle="tooltip" title="חתוך" data-image-path="" data-placement="bottom" @if(isset($cropName)) data-crop-name="{{$cropName}}" @endif data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>
                            <i class="fa fa-crop" aria-hidden="true"></i>
                        </li>
                    @endif
                </ul>
            </div>
            <img src="__THUMBNAIL_IMAGE__" class="image-input-placeholder">
            <input type="hidden" name="{{$name}}" class="file-name-input" value="__FILE_PATH__">
            @if(isset($clonerField))
                <div class="input-fields-container">
                    {!! $clonerField !!}
                </div>
            @endif
        </div>
    </div>
</script>
