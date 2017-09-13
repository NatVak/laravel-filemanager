
@extends(config('file-manager.layouts.popup'))

@section('content')

    <div style="padding-top:8px;">

        <form style="position:absolute;top:0;right:0" method="post" id="demo8_form" onsubmit="return ajaxSend()">
            @foreach($cropSizes as $size => $values)
                @if($values['canCrop'])
                    <input type="hidden" id="crop_x_{{$size}}" name="_CROP[{{$size}}][x]">
                    <input type="hidden" id="crop_y_{{$size}}" name="_CROP[{{$size}}][y]">
                    <input type="hidden" id="crop_w_{{$size}}" name="_CROP[{{$size}}][w]">
                    <input type="hidden" id="crop_h_{{$size}}" name="_CROP[{{$size}}][h]">
                    <input type="hidden" name="_CROP[{{$size}}][cutThis]" value="0" id="doCut_{{$size}}">
                @endif
            @endforeach

            <input type="hidden" name="image" value="{{url('uploads/original/'. $imagePath)}}">
        </form>
        <form id="cutOrigForm" method="post" onsubmit="return cutOriginal();">
            <input type="hidden" id="crop_x_Orig" name="x">
            <input type="hidden" id="crop_y_Orig" name="y">
            <input type="hidden" id="crop_w_Orig" name="w">
            <input type="hidden" id="crop_h_Orig" name="h">
            <input type="hidden" name="image" value="{{url('uploads/original/'. $imagePath)}}">
        </form>
        <div class="row modImages" id="files-crop">
            <div class="col-md-12">

                <div class="tabbable-custom ">
                    <ul class="nav nav-tabs ">
                        @foreach($cropSizes as $size => $values)

                            <li @if($loop->first) class="active" @endif>
                                <a href="#tab_{{$cropName}}_{{$size}}" data-toggle="tab" aria-expanded="true">{{$values['title']}}<span class="sizeCrop" style="float: left; padding-top: 3px; padding-right: 5px; font-size: 11px !important; text-align: center;">{{$values['width']}}x{{$values['height']}}</span></a>
                            </li>

                        @endforeach

                    </ul>
                    <div class="tab-content">
                        @foreach($cropSizes as $size => $values)

                            @php($maxBoxSize = 250)
                            @php($cropSmallImage = [])
                            @if($values['height'] > $maxBoxSize || $values['width'] > $maxBoxSize)
                                @if($values['width'] > $values['height'])
                                    @php($ratio = $maxBoxSize / $values['width'])
                                    @php($cropSmallImage['width'] = $maxBoxSize)
                                    @php($cropSmallImage['height'] = $values['height'] * $ratio)
                                @else
                                    @php($ratio = $maxBoxSize / $values['height'])
                                    @php($cropSmallImage['height'] = $maxBoxSize)
                                    @php($cropSmallImage['width'] = $values['width'] * $ratio)
                                @endif
                            @else
                                @php($ratio = 1)
                                @php($cropSmallImage['width'] = $values['width'])
                                @php($cropSmallImage['height'] = $values['height'])
                            @endif

                            <div  class="tab-pane @if($loop->first) active @endif" id="tab_{{$cropName}}_{{$size}}" style="position:relative;overflow: auto">
                                @if(!$values['canCrop'])
                                    @foreach($values['error'] as $error)
                                        <div class='col-md-8 alert alert-block alert-danger fade in'>
                                            <button type='button' class='close' data-dismiss='alert'></button>
                                            <h4 style='margin: 0 26px;line-height: 25px;font-weight: bold' class='alert-heading'>
                                                <i class='fa fa-exclamation-triangle' style="margin-left: 20px;"></i>{{ $error }}
                                            </h4>
                                        </div>
                                        <div style='clear:both'></div>
                                    @endforeach
                                @else
                                    <div class="row-fluid">
                                    <div class="col-md-8">
                                        <div class="mainCutImg">
                                            <img src="{{url('uploads/original/'. $imagePath)}}" id="cropBox{{$cropName . '_' . $size}}" alt="" />
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="direction: ltr">
                                        <div class="beforeImgBox">
                                            <span style="text-align: left;">Before</span>
                                            @php
                                                $end = strpos($values['fileCroppedPath'], '?');
                                            @endphp
                                            <div class="imgBlock" data-file-path="{{$values['fileCroppedPath']}}{{(!$end) ? '?v=' . str_random(10) : ''}}">
                                                @if($values['fileExists'])
                                                    <img src="{{$values['fileCroppedPath'] }}{{(!$end) ? '?v=' . str_random(10) : ''}}"/>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="afterImgBox">
                                            <span style="text-align: left;">After</span>
                                            <div class="imgBlock">
                                                <div style="width:{{ $cropSmallImage['width'] }}px;height:{{ $cropSmallImage['height'] }}px;">
                                                    <img data-ratio="{{ $ratio }}" id="preview_{{$cropName . '_' . $size}}" src="{{url('uploads/original/'. $imagePath)}}"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <button onclick="$('input[data-cropkey={{$size}}]').click();$('#demo8_form').submit();" class="btn green-haze btn">{{trans('file_manager::app.crop.buttons.save')}}</button>
                                            <div style="display: none">
                                                <input type="checkbox" data-cropkey="{{$size}}" class="toggleCrop">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@push('freeScripts')
<script type="text/javascript">

    var croppedImage = '';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': X_CSRF_TOKEN
        }
    });

    function ajaxSend()
    {
        App.blockUI();
        var url = GlobalPath + "/file-manager/crop/{{$imagePath}}";
        $.ajax({
            type: "POST",
            url: url,
            data: {POST:$("#demo8_form").serialize(),MODFILES:'{{json_encode($cropSizes)}}', cropName: '{{request('cropName')}}'},
            success: function(data)
            {
                $('.toggleCrop').prop('checked', false);
                $('#tab_{{request('cropName')}}_'+croppedImage+' .beforeImgBox .imgBlock').each(function() {
                    var hasImage = $(this).has('img').length;
                    if (hasImage) {
                        var image = $(this).find('img').attr('src');
                        image = image.split("?")[0].split("#")[0];
                        image = image + '?v=' + FileManagerModal.uuid();
                        $(this).empty();
                        $(this).html("<img src='" + image + "'>");
                    } else {
                        var src = $(this).attr('data-file-path');
                        var img = $('<img>');
                        img.attr('src', src);
                        $(this).append(img);
                    }
                });
                showToast('{{trans('file_manager::app.crop.messages.success.message')}}', '{{trans('file_manager::app.crop.messages.success.title')}}', 'success');
                App.unblockUI();
            },
            error: function() {
              showToast('{{trans('file_manager::app.crop.messages.error.message')}}', '{{trans('file_manager::app.crop.messages.error.title')}}', 'error');
                App.unblockUI();
            }
        });

        return false;

    }

</script>
<script type="text/javascript">
    @foreach($cropSizes as $size => $values)
        @if($values['canCrop'])
            var {{$cropName . '_' . $size . 'width'}}    = {{$values['width']}};
            var {{$cropName . '_' . $size . 'height'}}   = {{$values['height']}};
            var {{$cropName . '_' . $size . 'fromLeft'}} = {{$values['fromLeft']}};
            var {{$cropName . '_' . $size . 'fromTop'}}  = {{$values['fromTop']}};
        @endif
    @endforeach

    var imgWidth = {{$imgWidth}};
    var imgHeight = {{$imgHeight}};

    var tabWidth = 500;
    $(function($){
        //tabWidth = $('.tab-content').width();
        $('#cropBoxOriginal').Jcrop({
            onChange: showCoordsOriginal,
            onSelect: showCoordsOriginal,
            boxWidth: tabWidth,
        });


        var $imageRatio = imgWidth / imgHeight;

        @foreach($cropSizes as $size => $values)
            @if($values['canCrop'])
                var $currentCropRatio = {{$cropName . '_' . $size}}width / {{$cropName . '_' . $size}}height;
                if(
                        (($currentCropRatio < 1.32 && $currentCropRatio > 1.28) && ($imageRatio < 1.32 && $imageRatio > 1.28)) ||
                        (($currentCropRatio < 0.66 && $currentCropRatio > 0.61) && ($imageRatio < 0.66 && $imageRatio > 0.61))
                )
                {
                    var setSelect = [0,0,imgWidth,imgHeight];
                }
                else
                {
                    var setSelect = [{{$cropName . '_' . $size}}fromLeft,{{$cropName . '_' . $size}}fromTop,{{$cropName . '_' . $size}}fromLeft+{{$cropName . '_' . $size}}width,{{$cropName . '_' . $size}}fromTop+{{$cropName . '_' . $size}}height];

                    var widthRatio  = imgWidth  / {{$cropName . '_' . $size}}width;
                    var heightRatio = imgHeight / {{$cropName . '_' . $size}}height;

                    var smallestRatio = Math.min(widthRatio, heightRatio);
                    //if(smallestRatio > 2.5)
                    //{
                    if(smallestRatio == widthRatio)
                        var finalRatio = imgWidth / {{$cropName . '_' . $size}}width;
                    //var finalRatio = (imgWidth / 2.5) / exhibitions_x_smallwidth;
                    else
                        var finalRatio = imgHeight / {{$cropName . '_' . $size}}height;
                    //var finalRatio = (imgHeight / 2.5) / exhibitions_x_smallheight;

                    temp{{$cropName . '_' . $size}}width  = {{$cropName . '_' . $size}}width  * finalRatio;
                    temp{{$cropName . '_' . $size}}height = {{$cropName . '_' . $size}}height * finalRatio;

                    {{$cropName . '_' . $size}}fromTop  = ((imgHeight - temp{{$cropName . '_' . $size}}height) / 2);
                    {{$cropName . '_' . $size}}fromLeft = ((imgWidth - temp{{$cropName . '_' . $size}}width) / 2);

                    var setSelect = [{{$cropName . '_' . $size}}fromLeft,{{$cropName . '_' . $size}}fromTop,{{$cropName . '_' . $size}}fromLeft+temp{{$cropName . '_' . $size}}width,{{$cropName . '_' . $size}}fromTop+temp{{$cropName . '_' . $size}}height];
                    //}
                }

                $('#cropBox{{$cropName . '_' . $size}}').Jcrop({
                    onSelect:    showCoords_{{$cropName . '_' . $size}},
                    onChange:    showPreview_{{$cropName . '_' . $size}},
                    minSize: [ {{$cropName . '_' . $size}}width,{{$cropName . '_' . $size}}height ],
                    setSelect: setSelect,
                    bgColor: 'black',
                    allowResize: true,
                    aspectRatio:{{$cropName . '_' . $size}}width / {{$cropName . '_' . $size}}height,
                    boxWidth: tabWidth,
                });
            @endif

        @endforeach


        $('.toggleCrop').click(function(){
            var cropKey = $(this).data('cropkey');
                croppedImage = cropKey;
            if($(this).is(':checked'))
                $('#doCut_'+cropKey).val('1');
            else
                $('#doCut_'+cropKey).val('0');
        });

    });

    @foreach($cropSizes as $size => $values)
        @if($values['canCrop'])
            function showCoords_{{$cropName . '_' . $size}}(c)
            {
                showPreview_{{$cropName . '_' . $size}}(c);
                $('#crop_x_{{$size}}').val(c.x);
                $('#crop_y_{{$size}}').val(c.y);
                $('#crop_w_{{$size}}').val(c.w);
                $('#crop_h_{{$size}}').val(c.h);
            }

            function showPreview_{{$cropName . '_' . $size}}(coords)
            {
                var $ratio = $('#preview_{{$cropName . '_' . $size}}').attr('data-ratio');

                var rx = {{$cropName . '_' . $size}}width / coords.w;
                var ry = {{$cropName . '_' . $size}}height / coords.h;

                var rx = $ratio * rx ;
                var ry = $ratio * ry ;

                $('#preview_{{$cropName . '_' . $size}}').css({
                    width: Math.round(rx * {{$imgWidth}}) + 'px',
                    height: Math.round(ry * {{$imgHeight}}) + 'px',
                    marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                    marginTop: '-' + Math.round(ry * coords.y) + 'px'
                });
            }
        @endif
    @endforeach

    function showCoordsOriginal(c)
    {
        $('#crop_x_Orig').val(c.x);
        $('#crop_y_Orig').val(c.y);
        $('#crop_w_Orig').val(c.w);
        $('#crop_h_Orig').val(c.h);

        var maxSize = 250;
        var originalImgDiv = $('.row-fluid.original .afterImgBox .imgBlock div');
        var originalSaveBtn = $('.row-fluid.original button');
        var originalImg = $('#preview_cropOrig');
        if(c.w == 0 || c.h == 0)
        {
            originalImgDiv.css({width:'0',height:'0'});
            originalSaveBtn.addClass('disabled');
            return false;
        }

        originalSaveBtn.removeClass('disabled');

        if(c.w <= 250 && c.h <= 250)
        {
            console.log(c.w);
            var rx = c.w;
            var ry = c.h;

            originalImg.css({
                width: '1619px',
                height: '1080px',
                marginLeft: '-' + c.x + 'px',
                marginTop: '-' + c.y + 'px'
            });

            originalImgDiv.css({width:c.w + 'px',height:c.h + 'px'});
            return false;
        }
        if(c.w > c.h)
        {
            var $ratio = maxSize / c.w;
            var boxWidth = maxSize;
            var boxHeight = c.h * $ratio;
        }
        else
        {
            var $ratio = maxSize / c.h;
            var boxHeight = maxSize;
            var boxWidth = c.w * $ratio;
        }

        originalImg.css({
            width: Math.round(1619 * $ratio) + 'px',
            height: Math.round(1080 * $ratio) + 'px',
            marginLeft: '-' + Math.round(c.x * $ratio) + 'px',
            marginTop: '-' + Math.round(c.y * $ratio) + 'px'
        });

        originalImgDiv.css({width:boxWidth + 'px',height:boxHeight + 'px'});

    }
</script>

@endpush
