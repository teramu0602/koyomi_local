<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <script src="{{ asset('js/app.js') }}" defer></script>

        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">



    </head>

   <header>
        <div class="logo">logo</div>
        <h1 class="service-name">KOYOMI</h1>
        @yield('header')
        @yield('drop_menu')
    </header>

    <main>
    @yield('content')
    </main>
    <body>    
    </body>
    
</html>