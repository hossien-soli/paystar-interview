<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    @vite(['resources/sass/app.scss','resources/js/app.js'])
    @yield('head_links')
</head>
<body>
    @include('partials.navigation')
    
    <div class="container pt-3">
        @yield('content')
    </div>

    <script>
        @if(session('swalWarning'))
        swalWarning("{{ session('swalWarning') }}");
        @endif

        @if(session('swalError'))
        swalError("{{ session('swalError') }}");
        @endif

        @if(session('swalSuccess'))
        swalSuccess("{{ session('swalSuccess') }}");
        @endif
    </script>

    @yield('end_links')
</body>
</html>