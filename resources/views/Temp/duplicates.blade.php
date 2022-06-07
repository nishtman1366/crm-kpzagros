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

</head>
<body class="font-sans antialiased">
<div class="w-full flex items-center justify-center rtl">
    <div class="flex-1">
        <table class="min-w-full divide-y divide-gray-200">
            <tr>
                <th scope="col" class="list-table-header-cell">ردیف</th>
                <th scope="col" class="list-table-header-cell">شناسه پروفایل</th>
                <th scope="col" class="list-table-header-cell">شماره پذیرنده</th>
                <th scope="col" class="list-table-header-cell">شماره ترمینال</th>
                <th scope="col" class="list-table-header-cell">سرویس دهنده</th>
                <th scope="col" class="list-table-header-cell">مشتری</th>
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
                </tr>
                @php($i++)
            @endforeach
            </tbody>
        </table>
        {{--        <table class="min-w-full divide-y divide-gray-200">--}}
        {{--            <tr>--}}
        {{--                <th scope="col" class="list-table-header-cell">ردیف</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شناسه مشتری</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کد ملی</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شناسه پروفایل</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">سرویس‌دهنده</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شماره پذیرنده</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شماره ترمینال</th>--}}
        {{--            </tr>--}}
        {{--            <tbody>--}}
        {{--            @php($j=1)--}}
        {{--            @foreach($duplicates as $duplicate)--}}
        {{--                @if(count($duplicate['customers']) > 0)--}}
        {{--                    <tr class="bg-blue-100">--}}
        {{--                        <td class="list-table-body-cell">{{$j}}</td>--}}
        {{--                        <td class="list-table-body-cell">{{$duplicate['customers']->first()->id}}</td>--}}
        {{--                        <td class="list-table-body-cell">{{$duplicate['customers']->first()->national_code}}</td>--}}
        {{--                        <td class="list-table-body-cell">{{$duplicate['customers']->first()->profile_id}}</td>--}}
        {{--                        <td class="list-table-body-cell">{{$duplicate['customers']->first()->profile->psp->name}}</td>--}}
        {{--                        <td colspan="2"></td>--}}
        {{--                    </tr>--}}
        {{--                    @php($i=1)--}}
        {{--                    @foreach($duplicate['customers'] as $customer)--}}
        {{--                        <tr class="bg-red-100">--}}
        {{--                            <td class="list-table-body-cell">{{$i}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->id}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->national_code}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->profile_id}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->profile->psp->name}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->profile->merchant_id}}</td>--}}
        {{--                            <td class="list-table-body-cell">{{$customer->profile->terminal_id}}</td>--}}
        {{--                        </tr>--}}
        {{--                        @php($i++)--}}
        {{--                    @endforeach--}}
        {{--                    @php($j++)--}}
        {{--                @endif--}}
        {{--            @endforeach--}}
        {{--            </tbody>--}}
        {{--        </table>--}}


        {{--        <table class="min-w-full divide-y divide-gray-200">--}}
        {{--            <tr>--}}
        {{--                <th scope="col" class="list-table-header-cell">شناسه پروفایل</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کد ملی</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">نام</th>--}}
        {{--                <th scope="col" class="list-table-header-cell"> نام (انگلیسی)</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">نام پدر</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">نام پدر (انگلیسی)</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">جنسیت</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">موبایل</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">تاریخ تولد</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کسب و کار</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کسب و کار (انگلیسی)</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">صنف</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">آدرس</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کدپستی</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">تلفن</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">کد اقتصادی</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شماره پذیرنده</th>--}}
        {{--                <th scope="col" class="list-table-header-cell">شماره ترمینال</th>--}}
        {{--            </tr>--}}
        {{--            <tbody class="bg-white divide-y divide-gray-200">--}}
        {{--            @foreach($duplicates as $duplicate)--}}
        {{--                <tr>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->id}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->national_code}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->full_name}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->first_name_english.' '.$duplicate['customer']->first_name_english}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->father}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->father_english}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->genderText}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->mobile}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['customer']->jBirthday}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->name : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->name_english : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->senf : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->address : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->postal_code : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->phone : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->business ? $duplicate['profile']->business->tax_code : '-'}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->merchant_id}}</td>--}}
        {{--                    <td class="list-table-body-cell">{{$duplicate['profile']->terminal_id}}</td>--}}
        {{--                </tr>--}}
        {{--            @endforeach--}}
        {{--            </tbody>--}}
        {{--        </table>--}}
    </div>
</div>
</body>
</html>
