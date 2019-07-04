@extends( ($popup) ? config('file-manager.layouts.popup') : config('file-manager.layouts.main'))

@section('content')

    <script>
        GlobalPublicPath = '{{url('/')}}';
    </script>

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
                        <li class="buttons">
                            <a href="{{route_with_params('filemanager.upload', ['folderId' => $folderId])}}" class="btn blue" type="button"><i class="fa fa-cloud-upload" aria-hidden="true"></i> {{trans('file_manager::app.browser.buttons.upload_files')}}</a>
                            @if (!$folderId || config('file.manager.allow-sub-dir'))
                                <a href="{{route_with_params('filemanager.add_folder', ['folderId' => $folderId])}}" class="btn blue" type="button"><i class="fa fa-plus" aria-hidden="true"></i> {{trans('file_manager::app.browser.buttons.add_folder')}}</a>
                            @endif
                        </li>
                    </ul>
                </div>

                <div class="items">

                    <div class="background">
                        {{Form::open(['class' => 'form-horizontal', 'id' => 'filesForm' ])}}
                        @if($folders->count() || $files->count())
                            <div class="row actions">
                                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 search">
                                    <div class="form-group">
                                        <div class="input-icon">
                                            <i class="fa fa-search fa-fw"></i>
                                            <input class="form-control" type="text" name="stringSearch" id="stringSearch" value="" autocomplete="off" placeholder="{{trans('file_manager::app.browser.search_current_folder')}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 part1">
                                    <div class="links">
                                        <span>{{trans('file_manager::app.browser.actions.display')}}</span>
                                        <a href="javascript:void(0)" class="checkall">{{trans('file_manager::app.browser.actions.check_all')}}</a> <span>/</span>
                                        <a href="javascript:void(0)" class="uncheckall">{{trans('file_manager::app.browser.actions.uncheck_all')}}</a>
                                    </div>
                                    &nbsp;
                                    <span id="cntImgsChecked"><span></span> {{trans('file_manager::app.browser.actions.selected')}}</span>
                                    <div class="acts" style="padding-top: 5px; padding-bottom:5px; display:none;">
                                        <button type="submit" name="remove" class="btn btn-sm red" style="margin-top: -5px;" value="true" onclick="if(confirm('{{trans('file_manager::app.browser.actions.remove.are_you_sure')}}')){return true}else{return false};"><i class="fa fa-times"></i> {{trans('file_manager::app.browser.actions.remove.display')}}</button>
                                        <span class="moveOption">
                                                <span>&nbsp; {{trans('file_manager::app.browser.actions.or')}} &nbsp;</span>
                                                <select name="transferToFolder" id="transferToFolder" class="form-control">
                                                    @foreach($selectOptions as $option)
                                                        {!! $option !!}
                                                    @endforeach
                                                </select>
                                                <button type="submit" name="transfer" class="btn btn-sm blue" value="transfer" style="margin-top: -5px;"><i class="fa fa-mail-reply-all"></i> {{trans('file_manager::app.browser.actions.move')}}</button>
                                            </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row items_in">

                            @if($popup)
                                <div class="alert alert-info no-files">
                                    <button type="submit" name="addToPage" id="btnAddToPage" class="btn blue disabled">
                                        <i class="fa fa-cloud-upload"></i>
                                        <span id="cntImgsChecked2">{{(request('filesCount') ? request('filesCount') : 0)}}</span> {{(request('filetype') == 'image') ? trans('file_manager::app.browser.images_were_selected') : trans('file_manager::app.browser.files_were_selected')}}
                                    </button>
                                    <div style="margin-top: 5px; font-size: 12px; font-weight: normal;">
                                        <b>
                                            {{(request('filetype') == 'image') ? trans('file_manager::app.browser.choose_image') : trans('file_manager::app.browser.choose_file')}}.
                                        </b> <br />
                                        @if(request('newminfiles') != request('newmaxfiles'))
                                            <p><span> {{trans('file_manager::app.browser.select_options_from')}} </span>{{request('newminfiles')}} {{trans('file_manager::app.browser.select_options_to')}}{{request('newmaxfiles')}} {{(request('filetype') == 'image') ? trans('file_manager::app.browser.images') : trans('file_manager::app.browser.files')}}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($folders->count() || $files->count())
                                @foreach($folders as $folder)
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 file-manager-item">
                                        <div class="thumbnail">
                                            <div class="info"><a href="javascript:void(0);"><i class="fa fa-info-circle" aria-hidden="true"></i></a></div>
                                            <div class="info_btnClose"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></button></div>
                                            <div class="info_open">
                                                <div class="text">
                                                    <span class="infoName">{{$folder->name}}</span> <br />
                                                    <span>{{trans('file_manager::app.browser.details.date')}}</span> <span>{{$folder->created_at->format('d/m/y')}}</span> <br />
                                                    <span>{{trans('file_manager::app.browser.details.hour')}}</span> <span>{{$folder->created_at->format('H:i')}}</span><br />
                                                    <span>{{trans('file_manager::app.browser.details.size')}}</span><span>{{$folder->getSize()}}</span><br />
                                                </div>
                                            </div>
                                            <a href="{{route_with_params('filemanager.main', ['folderId' => $folder->id])}}">
                                                <div class="folder ui-droppable">
                                                    <div class="front">{{$folder->itemsCount()}} {{trans('file_manager::app.browser.items')}}</div>
                                                    <div class="back"></div>
                                                </div>
                                            </a>
                                            <div class="options">
                                                <input type="hidden" name="itemType[]" class="itemType" value="folder" />
                                                <input type="hidden" name="itemID[]"  class="itemID" value="{{$folder->id}}" />
                                                <input type="hidden" name="checkedItems[]"  class="checkedFiles" value="0" />
                                                <input type="text" name="itemName" autocomplete="off" value="{{$folder->name}}" class="inputEditName" />
                                                <input type="hidden" name="typeFileIsOk[]" class="typeFileIsOk" value="0" />
                                                <span style="display:none;"><img /></span>
                                                <span class="checkbox"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @foreach($files as $file)
                                    <div class="col-xs-12 col-sm-4 col-md-3 col-lg-2 file-manager-item">
                                        <div class="thumbnail file">
                                            <div class="info"><a href="javascript:void(0);"><i class="fa fa-info-circle" aria-hidden="true"></i></a></div>
                                            <div class="info_btnClose"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></button></div>
                                            <div class="info_open">
                                                <div class="text">
                                                    <span class="infoName">{{$file->name}}</span> <br />
                                                    <span>{{trans('file_manager::app.browser.details.date')}}</span> <span>{{$file->created_at->format('d/m/y')}}</span> <br />
                                                    <span>{{trans('file_manager::app.browser.details.hour')}}</span> <span>{{$file->created_at->format('H:i')}}</span><br />
                                                    <span>{{trans('file_manager::app.browser.details.size')}}</span> <span>{{$file->getSize()}}</span><br />
                                                </div>
                                            </div>
                                            @if(file_manager_check_file($file->extension, $file->size) && $popup && request('ismulti') == 'false')
                                                <div class="chooseOneFile"><button type="submit" name="addOneFileToPage" value="" class="btn blue">{{trans('file_manager::app.browser.add_to_page')}}</button></div>
                                            @endif
                                            <div class="image">
                                                <div class="typeFile">{{$file->extension}}</div>
                                                <img src="{{$file->getThumbnail()}}" onclick="window.open('{{url('uploads/original/' . $file->path)}}','{{$file->path}}','width=800,height=600')" />
                                            </div>
                                            <div class="options">
                                                <input type="hidden" name="itemType[]" class="itemType" value="file" />
                                                <input type="hidden" name="itemID[]" class="itemID" value="{{$file->id}}" />
                                                <input type="hidden" name="checkedItems[]" class="checkedFiles" value="0" />
                                                <input type="hidden" name="itemPath[]" class="itemPath" value="{{$file->path}}" />
                                                <input type="hidden" name="itemThumbnail[]" class="itemThumbnail" value="{{$file->getThumbnail()}}" />
                                                <input type="text" name="itemName" autocomplete="off" value="{{$file->name}}" class="inputEditName" />
                                                <span class="checkbox"></span>

                                                <input type="hidden" name="typeFileIsOk[]" class="typeFileIsOk" value="{{file_manager_check_file($file->extension, $file->size)}}" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info no-files">
                                    <strong>{{trans('file_manager::app.browser.no_files_to_display')}}</strong>
                                </div>
                            @endif

                        </div>
                        {{Form::close()}}
                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection

@push('readyScripts')
    var currentFolder = {{$folderId}};
    var popupOptions = {};
    @if($popup)
        popupOptions.isMulti            = '{{request('ismulti')}}';
        popupOptions.newMinFiles        = '{{request('newminfiles')}}';
        popupOptions.newMaxFiles        = '{{request('newmaxfiles')}}';
        popupOptions.allowedExtensions  = '{{(request('allowedExtensions') != '') ? request('allowedExtensions') : implode(config('file-manager.allowed_extensions'), ',')}}';
        popupOptions.inputId            = '{{request('inputId')}}';
        popupOptions.isFromPlugin       = '1';
        popupOptions.cntImgsChecked     = {{(request('filesCount')) ? request('filesCount') : 0}};
        popupOptions.fromTinyMce        = '{{(request('source') == 'tinymce') ? true : false}}'
    @endif
    var translations = {!! json_encode(trans('file_manager::app.js_lines')) !!};
    FileManagerBrowse.init(currentFolder, popupOptions, translations);
@endpush