@extends( ($popup) ? config('file-manager.layouts.popup') : config('file-manager.layouts.main'))

@section('content')

    @if(!$popup)
        <h1 class="page-title">{{trans('file_manager::app.title')}}</h1>
    @endif

    <div class="portlet light ">
        <div class="portlet-body">

            <div class="row modImages">

                    <div class="top">
                        <ul>
                            <li class="path">
                                <ul class="breadcrumb">
                                    <li>
                                        <a href="{{route_with_params('filemanager.main')}}">{{trans('file_manager::app.main_folder')}}</a>
                                        @if($parentsBreadcrumbs)
                                            <i class="fa fa-angle-{{(config('app.direction') == 'rtl') ? 'left' : 'right'}}"></i>
                                        @endif
                                    </li>
                                    @if($parentsBreadcrumbs)
                                        @foreach($parentsBreadcrumbs as $item)
                                            @if($loop->last)
                                                <li><a href="{{$item['url']}}">{{$item['title']}}</a></li>
                                            @else
                                                <li>
                                                    <a href="{{$item['url']}}">{{$item['title']}}</a>
                                                    <i class="fa fa-angle-{{(config('app.direction') == 'rtl') ? 'left' : 'right'}}"></i>
                                                </li>
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="items">
                        <div class="background">

                            <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                                <div class="slides">
                                </div>
                                <h3 class="title"></h3>
                                <a class="prev">
                                    ‹ </a>
                                <a class="next">
                                    › </a>
                                <a class="close white">
                                </a>
                                <a class="play-pause">
                                </a>
                                <ol class="indicator">
                                </ol>
                            </div>
                            <script id="template-upload" type="text/x-tmpl">
                                {% for (var i=0, file; file=o.files[i]; i++) { %}
                                    <tr class="template-upload fade">
                                        <td>
                                            <span class="preview"></span>
                                        </td>
                                        <td>
                                            <p class="name">{%=file.name%}</p>
                                            <strong class="error text-danger label label-danger"></strong>
                                        </td>
                                        <td>
                                            <p class="size">{{trans('file_manager::app.upload.in_progress')}}...</p>
                                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            {% if (!i && !o.options.autoUpload) { %}
                                                <button class="btn blue start" disabled @if(request('ismulti') == 'true') style="display:none;" @endif>
                                                    <i class="fa fa-upload"></i>
                                                    <span>{{trans('file_manager::app.upload.buttons.upload')}}</span>
                                                </button>
                                            {% } %}
                                            {% if (!i) { %}
                                                <button class="btn red cancel">
                                                    <i class="fa fa-ban"></i>
                                                    <span>{{trans('file_manager::app.upload.buttons.cancel')}}</span>
                                                </button>
                                            {% } %}
                                        </td>
                                    </tr>
                                {% } %}
                            </script>
                            <!-- The template to display files available for download -->
                            <script id="template-download" type="text/x-tmpl">
                                {% for (var i=0, file; file=o.files[i]; i++) { %}
                                    <tr class="template-download fade">
                                        <td>
                                            <span class="preview">
                                                {% if (file.thumbnailUrl) { %}
                                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                                                {% } %}
                                            </span>
                                        </td>
                                        <td>
                                            <p class="name">
                                                {% if (file.url) { %}
                                                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.newFileName%}</a>
                                                {% } else { %}
                                                    <span>{%=file.newFileName%}</span>
                                                {% } %}
                                            </p>
                                            {% if (file.error) { %}
                                                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                                            {% } %}
                                        </td>
                                        <td>
                                            <span class="size">{%=o.formatFileSize(file.size)%}</span>
                                        </td>
                                        <td>
                                            {% if (file.deleteUrl) { %}
                                                <button class="btn red delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}><i class="fa fa-trash-o"></i><span>{{trans('file_manager::app.upload.buttons.delete')}}</span></button>

                                            {% } else { %}
                                                <button class="btn yellow cancel btn-sm"><i class="fa fa-ban"></i><span>{{trans('file_manager::app.upload.buttons.cancel')}}</span></button>
                                            {% } %}
                                        </td>
                                    </tr>
                                {% } %}
                            </script>

                            <form id="fileupload" action="{{ route_with_params('filemanager.do_upload', ['folderId' => $folderId])}}" method="POST" enctype="multipart/form-data" accept-charset="utf-8">
                                <div class="row fileupload-buttonbar">
                                    <div class="col-lg-7">
                                        <span class="btn green fileinput-button"><i class="fa fa-plus"></i><span>{{trans('file_manager::app.upload.buttons.add_files')}}</span><input type="file" @if(request('ismulti') == 'false') name="files[]" @else name="files[]" multiple="" @endif></span>
                                        <button type="submit" class="btn blue start"><i class="fa fa-upload"></i><span>{{trans('file_manager::app.upload.buttons.start_upload')}}</span></button>
                                        <span class="fileupload-process"></span>
                                        <button type="reset" class="btn warning cancel"><i class="fa fa-ban-circle"></i><span>{{trans('file_manager::app.upload.buttons.cancel_upload')}}</span></button>
                                    </div>
                                    <div class="col-lg-5 fileupload-progress fade">
                                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar progress-bar-success" style="width:0%;">
                                            </div>
                                        </div>
                                        <div class="progress-extended">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                <!-- The table listing the files available for upload/download -->
                                <table role="presentation" class="table table-striped clearfix">
                                    <tbody class="files">
                                    </tbody>
                                </table>
                            </form>
                            <div class="panel panel-success" style="width: 98%; margin:0 1% 10px 1%;">
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{trans('file_manager::app.upload.instructions.title')}}</h3>
                                </div>
                                <div class="panel-body upload-info-details">
                                    <ul>
                                        <li><span>{{trans('file_manager::app.upload.instructions.max_file_size_is')}}</span> @if($popup && request('maxfilesize') != '') {{(round(request('maxfilesize') / 1000000)) . 'MB'}} @else {{config('file-manager.max_file_size') . 'MB'}} @endif </li>
                                        <li><span>{{trans('file_manager::app.upload.instructions.allowed_file_extensions_are')}}</span> @if($popup && request('allowedExtensions') != '') {{request('allowedExtensions')}} @else {{implode(config('file-manager.allowed_extensions'), ', ')}} @endif</li>
                                        <li><span>{{trans_choice('file_manager::app.upload.instructions.on_upload_x_crops_are_saved', count(config('file-manager.crop')) + 1, ['number' => count(config('file-manager.crop')) + 1])}}</span><br />
                                             <ol>
                                                 <li>{{trans('file_manager::app.upload.instructions.crops.original')}}</li>
                                                 @foreach(config('file-manager.crop') as $size => $values)
                                                     <li>{{$values['display_name']}} - <span>{{trans('file_manager::app.upload.instructions.crops.height')}} </span> {{$values['height']}} {{trans('file_manager::app.upload.instructions.crops.pixels')}}, <span>{{trans('file_manager::app.upload.instructions.crops.width')}} </span> {{$values['width']}} {{trans('file_manager::app.upload.instructions.crops.pixels')}}.</li>
                                                 @endforeach
                                             </ol>
                                        </li>
                                        <li>{{trans('file_manager::app.upload.instructions.alt_will_be_file_name')}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

@endsection

@push('readyScripts')

@if($popup)
    var maxFileSize = {{(request('maxfilesize') != '') ? request('maxfilesize') : config('file-manager.max_file_size') * 1000000}};
    @if(request('allowedExtensions') != '')
        var acceptFileTypes = /(\.|\/)({{str_replace(',', '|', request('allowedExtensions'))}})$/i;
    @else
        var acceptFileTypes = /(\.|\/)({{implode(config('file-manager.allowed_extensions'), '|')}})$/i;
    @endif
    var maxNumberOfFiles = {{(request('ismulti') == 'false') ? 1 : request('newmaxfiles')}};
    var popup = 1;
    var inputId = '{{request('inputId')}}';
    var isMulti = '{{request('ismulti')}}';
    var cntImgsChecked = {{(request('filesCount')) ? request('filesCount') : 0}};
    var fromTinyMce = '{{(request('source') == 'tinymce') ? true : false}}';
@else
    var maxFileSize = {{config('file-manager.max_file_size') * 1000000}};
    var acceptFileTypes = /(\.|\/)({{implode(config('file-manager.allowed_extensions'), '|')}})$/i;
    var maxNumberOfFiles = false;
    var popup = 0;
    var isMulti = true;
    var inputId = false;
    var cntImgsChecked = 0;
    var fromTinyMce = 'false';
@endif

var translations = {!! json_encode(trans('file_manager::app.js_lines')) !!};

FileManagerUpload.init(maxFileSize, acceptFileTypes, maxNumberOfFiles, popup, inputId, isMulti, cntImgsChecked, translations, fromTinyMce);
@endpush
