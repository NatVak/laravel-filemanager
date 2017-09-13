<!DOCTYPE html>
<html lang="en" dir="{{config('app.direction')}}">
<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="{{ asset((config('app.direction') == 'rtl') ? 'assets/admin/metronic/css/all-rtl.css' : 'assets/admin/metronic/css/all.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset((config('app.direction') == 'rtl') ? 'assets/admin/css/app-rtl.css' : 'assets/admin/css/app.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        var GlobalPath = '{{url('/admin')}}';
    </script>
</head>

<body class="file-manager-popup">

        <div class="page-content">
            @yield('content')
        </div>

<script>
    X_CSRF_TOKEN = '{{csrf_token()}}';
</script>
<script src="{{ asset('assets/admin/metronic/js/all.js') }}"></script>
<script src="{{ asset('assets/admin/metronic/js/custom.js') }}"></script>

<script>


    function showToast(message, title, type, options) {
        var originalOptions = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        for(k in originalOptions) {
            if(!originalOptions.hasOwnProperty(k)) {
                if(options && options[k]){
                    originalOptions[k] = options[k];
                }
            }
        }

        toastr.options = originalOptions;

        if(!type)
            type = 'success';

        if(title)
            toastr[type](message, title);
        else
            toastr[type](message);
    }

    @stack('regularScripts')

    $(document).ready(function(){

        App.setGlobalImgPath('/img/');

        App.setAssetsPath('{{url('assets/admin/metronic')}}');

        @if(session('toastr'))
                showToast('{{ session('toastr')['message'] }}', {!! isset(session('toastr')['title']) ? "'".session('toastr')['title']."'" : 'false' !!}, {!!isset(session('toastr')['type']) ? "'".session('toastr')['type']."'" : 'false'!!});
        @endif

        @stack('readyScripts')
    });
</script>
    @stack('freeScripts')
</body>
</html>