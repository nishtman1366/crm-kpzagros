<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ systemConfig('PAGE_TITLE', 'app.name') }}</title>

    <!-- Styles -->
    {{--    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{$version[1]}}">--}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/custom.css') }}">

</head>
<body class="font-sans antialiased">
<div class="w-full h-screen flex items-center justify-center rtl">
    <div>
        <a href="{{sprintf('https://kpzagros-app.ir/ipg/%s/settlement?status=ok',$referenceCode)}}">
            <button class="bg-green-300 hover:bg-green-400 py-2 px-3 rounded mx-2 shadow-sm transition">پرداخت موفق
            </button>
        </a>
        <a href="{{sprintf('https://kpzagros-app.ir/ipg/%s/settlement?status=error',$referenceCode)}}">
            <button class="bg-red-300 hover:bg-red-400 py-2 px-3 rounded mx-2 shadow-sm transition">پرداخت ناموفق
            </button>
        </a>
    </div>
</div>
</body>
</html>
