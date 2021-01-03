<?php

use Illuminate\Pagination\UrlWindow;

if (!function_exists('paginationLinks')) {
    function paginationLinks(Illuminate\Contracts\Pagination\LengthAwarePaginator $lengthAwarePaginator)
    {

        $window = UrlWindow::make($lengthAwarePaginator);

        $isCurrentPageSet = false;

        $array = array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
        $i = 1;
        foreach ($array as $index => $urlsArray) {

            if (is_array($urlsArray)) {
                foreach ($urlsArray as $pageNumber => $link) {
                    $currentPage = $lengthAwarePaginator->currentPage();
                    $n[] = [
                        'pageNumber' => $pageNumber,
                        'url' => $link,
                        'indexKey' => $i,
                        'type' => 'URLS',
                        'isCurrentPage' => $currentPage === $pageNumber,
                    ];
                    $i++;
                }
            } elseif (is_string($urlsArray)) {
                $n[] = [
                    'url' => $urlsArray,
                    'indexKey' => $i,
                    'type' => 'ELIPSIS',
                ];
                $i++;
            }

        }

        return count($n) === 1 ? null : $n;
    }
}

if (!function_exists('generateRandomColor')) {
    function generateRandomColor()
    {
        $color = '#';
        $colorHexLighter = array("7", "8", "9", "A", "B", "C", "D");
        for ($x = 0; $x < 6; $x++) {
            $color .= $colorHexLighter[array_rand($colorHexLighter, 1)];
        }
        return substr($color, 0, 7);
    }
}

if (!function_exists('systemConfig')) {
    function systemConfig($key, $default = null)
    {
        $value = \App\Models\Setting::where('key', $key)->get()->first();
        if (is_null($value)) return $default;

        return $value->value;
    }
}

if (!function_exists('toEnglishNumbers')) {
    function toEnglishNumbers($string = null)
    {
        if (is_null($string)) return null;
        if (is_array($string)) {
            $x = [];
            foreach ($string as $item) {
                $x[] = toEnglishNumbers($item);
            }
            return $x;
        } else {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $arabic = ['٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠'];

            $num = range(0, 9);
            $convertedPersianNums = str_replace($persian, $num, $string);
            return str_replace($arabic, $num, $convertedPersianNums);
        }
    }
}

if (!function_exists('clearAddress')) {
    function clearAddress($address)
    {
        return preg_replace('/\s+/', ' ', str_replace([',', '-', '/', '،', '?', '؟'], ' ', $address));
    }
}
