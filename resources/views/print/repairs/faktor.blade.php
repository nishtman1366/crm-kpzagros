<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ systemConfig('PAGE_TITLE', 'app.name') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/custom.css') }}">

</head>
<body>
<header>
    <nav>
        <div class="w-full py-2 text-center text-2xl bg-gray-100">
            کیان پرداز زاگرس
        </div>
        <div class="flex items-center mx-3 p-3">
            <div class="flex-grow">
                <h1 class="text-xl">صورتحساب خدمات</h1>
            </div>
            <div>
                <div>تاریخ: <span class="font-bold">{{$repair->jCreatedAt}}</span></div>
                <div>کد رهگیری: <span class="font-bold">{{$repair->tracking_code}}</span></div>
            </div>
        </div>
    </nav>
</header>
<main>
    <section class="my-2 mx-3 p-3 bg-gray-100">
        <div class="grid grid-cols-2 gap-3">
            <div>نام پذیرنده: <span class="font-bold">{{$repair->name}}</span></div>
            <div>مدل دستگاه: <span class="font-bold">{{!is_null($repair->deviceType) ? $repair->deviceType->name : 'نامشخص'}}</span> </div>
            <div>شماره تماس:<span class="font-bold">{{$repair->mobile}}</span></div>
            <div>سریال دستگاه: <span class="font-bold">{{$repair->serial}}</span> </div>
        </div>
    </section>
    <section class="p-3 mx-3">
        <div class="my-4">
            <p class="mb-3 text-lg font-bold">ایرادات اعلام شده توسط مشتری:</p>
            @foreach($repairTypesList as $item)
                <span class="mx-1 my-2 px-1">{{$item->type->name}}</span>
            @endforeach
        </div>
        <div class="my-4">
            <p class="mb-3 text-lg font-bold">اقلام همراه دستگاه:</p>
            <p>{{$repair->accessoryList}}</p>
        </div>
        <div>
            <p class="mb-3 text-lg font-bold">توضیحات تکمیلی واحد پذیرش:</p>
            <p class="text-justify">{{$repair->description}}</p>
        </div>
    </section>
    <section class="p-3 my-2 mx-3 bg-gray-100">
        <div class="my-4">
            <p class="mb-3 text-lg font-bold">گزارش عملیات:</p>
            <p class="text-justify">{{$repair->technical_description}}</p>
        </div>
    </section>
</main>
</body>
</html>
