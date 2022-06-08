<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ systemConfig('PAGE_TITLE', 'app.name') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/custom.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="font-sans antialiased">
<div class="w-full flex items-center justify-center rtl">
    <div class="flex-1">
        <div class="p-3 flex items-center justify-center flex-wrap">
            @foreach($psps as $psp)
                <a class="py-2 px-3 m-1 {{!is_null($pspId) && $psp->id==$pspId ? 'bg-green-600 text-gray-100' : 'bg-green-200 text-gray-700'}} rounded hover:bg-green-400 transition"
                   href="{{route('duplicates',['psp'=>$psp->id])}}">{{$psp->name}}</a>
            @endforeach
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <tr>
                <th scope="col" class="list-table-header-cell">ردیف</th>
                <th scope="col" class="list-table-header-cell">شناسه پروفایل</th>
                <th scope="col" class="list-table-header-cell">شماره پذیرنده</th>
                <th scope="col" class="list-table-header-cell">شماره ترمینال</th>
                <th scope="col" class="list-table-header-cell">سرویس دهنده</th>
                <th scope="col" class="list-table-header-cell">مشتری</th>
                <th scope="col" class="list-table-header-cell">کد ملی</th>
                <th scope="col" class="list-table-header-cell">نام کسب و کار</th>
                <th scope="col" class="list-table-header-cell">کد پستی</th>
            </tr>
            <tbody class="bg-white divide-y divide-gray-200">
            @php($i=1)
            @foreach($duplicates as $profile)
                <tr>
                    <td class="list-table-body-cell">{{$i}}</td>
                    <td class="list-table-body-cell">{{$profile->id}}</td>
                    <td class="list-table-bodبررy-cell">{{$profile->merchant_id}}</td>
                    <td class="list-table-body-cell">{{$profile->terminal_id}}</td>
                    <td class="list-table-body-cell">{{$profile->psp ? $profile->psp->name : ''}}</td>
                    <td class="list-table-body-cell">{{$profile->customer ? $profile->customer->fullName : ''}}</td>
                    <td class="list-table-body-cell">{{$profile->customer ? $profile->customer->national_code : ''}}</td>
                    <td class="list-table-body-cell">{{$profile->business ? $profile->business->name : ''}}</td>
                    <td class="list-table-body-cell">{{$profile->business ? $profile->business->postal_code : ''}}</td>
                </tr>
                @php($i++)
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
