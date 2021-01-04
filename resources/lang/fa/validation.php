<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted" => ":attribute باید پذیرفته شده باشد.",
    "active_url" => "آدرس :attribute معتبر نیست",
    "after" => ":attribute باید تاریخی بعد از :date باشد.",
    "alpha" => ":attribute باید شامل حروف الفبا باشد.",
    "alpha_dash" => ":attribute باید شامل حروف الفبا و عدد و خظ تیره(-) باشد.",
    "alpha_num" => ":attribute باید شامل حروف الفبا و عدد باشد.",
    "array" => ":attribute باید شامل آرایه باشد.",
    "before" => ":attribute باید تاریخی قبل از :date باشد.",
    "between" => [
        "numeric" => ":attribute باید بین :min و :max باشد.",
        "file" => ":attribute باید بین :min و :max کیلوبایت باشد.",
        "string" => ":attribute باید بین :min و :max کاراکتر باشد.",
        "array" => ":attribute باید بین :min و :max آیتم باشد.",
    ],
    "boolean" => ":attribute فقط میتواند صحیح و یا غلط باشد",
    "confirmed" => ":attribute با تاییدیه مطابقت ندارد.",
    "date" => ":attribute یک تاریخ معتبر نیست.",
    "date_format" => ":attribute با الگوی :format مطاقبت ندارد.",
    "different" => ":attribute و :other باید متفاوت باشند.",
    "digits" => ":attribute باید :digits رقم باشد.",
    "digits_between" => ":attribute باید بین :min و :max رقم باشد.",
    "email" => "فرمت :attribute معتبر نیست.",
    "exists" => ":attribute انتخاب شده، معتبر نیست.",
    "filled" => ":attribute الزامی است",
    "image" => ":attribute باید تصویر باشد.",
    "in" => ":attribute انتخاب شده، معتبر نیست.",
    "integer" => ":attribute باید نوع داده ای عددی (integer) باشد.",
    "ip" => ":attribute باید IP آدرس معتبر باشد.",
    "max" => [
        "numeric" => ":attribute نباید بزرگتر از :max باشد.",
        "file" => ":attribute نباید بزرگتر از :max کیلوبایت باشد.",
        "string" => ":attribute نباید بیشتر از :max کاراکتر باشد.",
        "array" => ":attribute نباید بیشتر از :max آیتم باشد.",
    ],
    "mimes" => ":attribute باید یکی از فرمت های :values باشد.",
    "min" => [
        "numeric" => ":attribute نباید کوچکتر از :min باشد.",
        "file" => ":attribute نباید کوچکتر از :min کیلوبایت باشد.",
        "string" => ":attribute نباید کمتر از :min کاراکتر باشد.",
        "array" => ":attribute نباید کمتر از :min آیتم باشد.",
    ],
    "not_in" => ":attribute انتخاب شده، معتبر نیست.",
    "numeric" => ":attribute باید شامل عدد باشد.",
    "regex" => ":attribute یک فرمت معتبر نیست",
    "required" => ":attribute الزامی است",
    "required_if" => ":attribute هنگامی که :other برابر با :value است، الزامیست.",
    "required_with" => ":attribute الزامی است زمانی که :values موجود است.",
    "required_with_all" => ":attribute الزامی است زمانی که :values موجود است.",
    "required_without" => ":attribute الزامی است زمانی که :values موجود نیست.",
    "required_without_all" => ":attribute الزامی است زمانی که :values موجود نیست.",
    "same" => ":attribute و :other باید مانند هم باشند.",
    "size" => [
        "numeric" => ":attribute باید برابر با :size باشد.",
        "file" => ":attribute باید برابر با :size کیلوبایت باشد.",
        "string" => ":attribute باید برابر با :size کاراکتر باشد.",
        "array" => ":attribute باسد شامل :size آیتم باشد.",
    ],
    "string" => "The :attribute must be a string.",
    "timezone" => ":attribute باید یک منطقه صحیح باشد.",
    "unique" => ":attribute قبلا انتخاب شده است.",
    "url" => "فرمت آدرس :attribute اشتباه است.",
    'starts_with' => ':attribute باید با :values شروع شود.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */
    'attributes' => [
        "name" => "نام",
        "username" => "نام کاربری",
        "email" => "پست الکترونیکی",
        "national_code" => "شماره ملی",
        "id_code" => "شماره شناسنامه",
        "birthday" => "تاریخ تولد",
        "first_name" => "نام",
        "first_name_english" => "نام انگلیسی",
        "last_name" => "نام خانوادگی",
        "last_name_english" => "نام خانوادگی انگلیسی",
        "family" => "نام خانوادگی",
        'father' => 'نام پدر',
        'father_english' => 'نام پدر انگلیسی',
        "password" => "رمز عبور",
        "password_confirmation" => "تاییدیه ی رمز عبور",
        "city" => "شهر",
        "country" => "کشور",
        "address" => "نشانی",
        "phone" => "تلفن",
        "mobile" => "تلفن همراه",
        "age" => "سن",
        "sex" => "جنسیت",
        "gender" => "جنسیت",
        "day" => "روز",
        "month" => "ماه",
        "year" => "سال",
        "hour" => "ساعت",
        "minute" => "دقیقه",
        "second" => "ثانیه",
        "title" => "عنوان",
        "text" => "متن",
        "content" => "محتوا",
        "description" => "توضیحات",
        "excerpt" => "گلچین کردن",
        "date" => "تاریخ",
        "time" => "زمان",
        "available" => "موجود",
        "size" => "اندازه",
        "file" => "فایل",
        "fullname" => "نام کامل",
        "content_category_id" => 'دسته بندی',
        "language_id" => 'زبان',
        "body" => 'متن',

        'national_card_file_1' => 'تصویر روی کارت ملی',
        'national_card_file_2' => 'تصویر پشت کارت ملی',
        'id_file' => 'تصویر شناسنامه',

        'company_name' => 'نام شرکت',
        'company_name_english' => 'نام شرکت به انگلیسی',
        'business_name' => 'نام تجاری',
        'company_reg_date' => 'تاریخ ثبت',
        'company_reg_code' => 'شماره ثبت',
        'company_national_code' => 'شناسه ملی',
        'asasname_file' => 'تصویر اساسنامه',
        'agahi_file_1' => 'تصویر آگهی ثبت',
        'agahi_file_2' => 'تصویر آگهی تاسیس',

        'ostan_id' => 'استان',
        'shahrestan_id' => 'شهرستان',
        'bakhsh_id' => 'بخش',
        'shahr_id' => 'شهر',

        'name_english' => 'نام انگلیسی',

        'senf' => 'نام صنف',
        'postal_code' => 'کدپستی',
        'phone_code' => 'پیش شماره',
        'esteshhad_file' => 'تصویر اشتهادنامه',
        'license_code' => 'شماره جواز کسب',
        'license_date' => 'تاریخ صدور جواز کسب',
        'license_file' => 'تصویر جواز کسب',

        'accounts.*.bank_id' => 'نام بانک',
        'accounts.*.branch' => 'کد شعبه',
        'accounts.*.sheba_file' => 'فایل تایید شماره شبا',
        'accounts.*.account_number' => 'شماره حساب',
        'accounts.*.sheba_code' => 'شماره شبا',
        'accounts.*.first_name' => 'نام صاحب حساب',
        'accounts.*.last_name' => 'نام خانوادگی صاحب حساب',
        'accounts.*.birthday' => 'تاریخ تولد',
        'accounts.*.national_code' => 'کد ملی',
        'accounts.*.mobile' => 'شماره موبایل',

        'device_id' => 'شماره سریال دستگاه',

        'terminal_id' => 'شماره پایانه',
        'merchant_id' => 'شماره پذیرنده',
        'psp_id' => 'شرکت خدمات دهنده',
        'message' => 'متن خطا',


        'device_connection_type_id' => 'نوع ارتباط',
        'device_type_id' => 'مدل دستگاه',
        'serial' => 'شماره سریال',
        'guarantee_start' => 'تاریخ شروع گارانتی',
        'guarantee_end' => 'تاریخ پایان گارانتی',

        'status' => 'وضعیت',
        'cancel_reason' => 'دلیل فسخ',
        'change_reason' => 'دلیل جابجایی',
        'change_message' => 'دلیل رد درخواست جابجایی',
        'new_device_id' => 'سریال جدید',

        'repairTypeList' => 'ایراد دستگاه',

        'ref_code' => 'کد پیگیری پرداخت',
        'payment_date' => 'تاریخ پرداخت',


        'pattern'=>'کد الگو',
        'notification_type_id'=>'نوع اعلان',
        'event'=>'نوع رخداد',
        'level'=>'دریافت کننده',
    ],
];
