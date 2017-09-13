<!DOCTYPE html>
<html lang="en" dir="{{config('app.direction')}}">
<head>
    <meta charset="utf-8" />
    <title>{{config('app.name')}}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    @include('FileManager::partials.styles')
    @routes
</head>

<body class="file-manager-popup">

    <div class="page-content">
        @yield('content')
    </div>

@include('FileManager::partials.scripts')

<script>

    @stack('regularScripts')

    $(document).ready(function (){
        @stack('readyScripts')
    });
</script>
    @stack('freeScripts')
</body>
</html>