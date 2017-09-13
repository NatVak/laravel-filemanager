<div class="file-multi-container" data-id="{{uniqid('file-manager-container-')}}">
    <div class="file-multi-options">
        <button class="upload-files" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"true"' data-fileType='"file"' data-newMinFiles='"{{((isset($minFiles) && $minFiles) ? $minFiles : 1)}}"' data-newMaxFiles='"{{((isset($maxFiles) && $maxFiles) ? $maxFiles : 99999)}}"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>העלה לענן</button>
        <button class="select-files" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"true"' data-fileType='"file"' data-newMinFiles='"{{((isset($minFiles) && $minFiles) ? $minFiles : 1)}}"' data-newMaxFiles='"{{((isset($maxFiles) && $maxFiles) ? $maxFiles : 99999)}}"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif>בחר מהענן</button>
        <button class="red-button remove-files">נקה הכל</button>
    </div>
    <div class="files-containers">

        @if(get_old_inputs_as_array($name))
            @php($defaultImages = get_old_inputs_as_array($name))
        @endif

        @if($defaultFiles)
            @foreach($defaultFiles as $defaultFile)
                <div class="fileinput @if ($errors->has($name)) has-error @endif ui-state-default multi" data-id="{{uniqid('file-manager-')}}">
                    <div class="fileinput-new thumbnail">
                        <div class="options">
                            <ul>
                                <li class="remove-red tooltips remove-image-multi" data-toggle="tooltip" title="הסר" data-placement="bottom">
                                    <i class="fa fa-times"></i>
                                </li>
                                <li class="tooltips open-file" data-toggle="tooltip" title="פתח" data-file="{{url('uploads/original/'.$defaultFile)}}" data-placement="bottom">
                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                </li>
                                <li class="tooltips select-file" data-toggle="tooltip" title="בחר" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                                    <i class="fa fa-cloud" aria-hidden="true"></i>
                                </li>
                                <li class="tooltips upload-file" data-toggle="tooltip" title="העלה" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                                    <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                </li>
                            </ul>
                        </div>
                        <img src="{{get_file_thumbnail($defaultFile)}}" class="image-input-placeholder">
                        <input type="hidden" name="{{$name}}" class="file-name-input" value="{{$defaultFile}}">
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
                    <li class="tooltips select-file" data-toggle="tooltip" title="בחר" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud" aria-hidden="true"></i>
                    </li>
                    <li class="tooltips upload-file" data-toggle="tooltip" title="העלה" data-placement="bottom" @if($maxFileSize) data-maxfilesize="'{{$maxFileSize}}'" @endif  data-placement="bottom" data-isMulti='"false"' data-fileType='"file"' data-newMinFiles='"1"' data-newMaxFiles='"1"' data-allowed-extensions="{{implode(',', $allowedExtensions)}}" @if(isset($attributes)) {{ implode(' ', $attributes) }} @endif >
                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                    </li>
                </ul>
            </div>
            <img src="__THUMBNAIL_IMAGE__" class="image-input-placeholder">
            <input type="hidden" name="{{$name}}" class="file-name-input" value="__FILE_PATH__">
            @if(isset($fields))
                <div class="input-fields-container">
                    {!! $fields !!}
                </div>
            @endif
        </div>
    </div>
</script>
