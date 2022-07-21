<style>
    @page {
        header: page-header
    }

    html {
        font-size: 12px;
        direction: rtl;
    }

    body {
        font-size: 11px;
        font-family: fa;
        line-height: 1rem;
        direction: rtl;
    }

    #logo {
        position: absolute;
        top: 6rem;
        left: 6rem;
    }

    #logo img {
        width: 180px;
    }

    #container {
        width: 100%;
        margin: 0 3px;
        line-height: 1rem;
        direction: rtl;
    }

    .pre-title {
        font-size: .9rem;
        text-align: center;
        direction: rtl;
    }

    .title {
        font-size: 1.5rem;
        text-align: center;
        margin: 3rem 0;
        font-weight: bold;
        direction: rtl;
    }

    .contents {
        text-align: justify;
        line-height: 2rem;
        direction: rtl;
    }

    .variables {
        font-weight: bold;
        background-color: #EAEAEA;
        border-radius: .5rem;
        margin: 0 .5rem;
        padding: .5rem;
        direction: rtl;
    }

    table.items {
        width: 100%;
        margin: 0 auto;
        border: 1px solid #EAEAEA;
        border-radius: .5rem;
        padding: .5rem;
        direction: rtl;
    }

    table.items td {
        padding: .25rem;
        width: 20%;
        direction: rtl;
    }

    table.items td:nth-child(2n+1) {
        font-weight: bold;
        text-align: left;
        direction: rtl;
    }

    table.signs {
        width: 100%;
    }

    table.signs td {
        width: 33%;
        text-align: center;
    }

    table.signs td p {
        font-size: 1rem;
    }
</style>
<htmlpageheader name="page-header">
    <div id="logo">
        <div>تاریخ: {{toPersianNumbers(\Morilog\Jalali\Jalalian::now()->format('Y/m/d'))}}</div>
    </div>
</htmlpageheader>

<div id="container">
    <div class="pre-title">بسمه تعالی</div>
    <div class="title">رسید نصب و تحویل دستگاه کارتخوان – نسخه پذیرنده</div>
    <div class="contents">
        گواهی می شود یک عدد دستگاه کارتخوان مدل <span
            class="variables">{{is_null($terminal->deviceType) ? 'ثبت نشده' : $terminal->deviceType->name}}</span> به صورت
        <span
            class="variables">{{$terminal->deviceSellTypeText}}</span> و متصل
        به حساب بانک <span
            class="variables">{{(!is_null($profile->accounts) && count($profile->accounts) > 0 && !is_null($profile->accounts->first()->account)) ? toPersianNumbers($profile->accounts->first()->account->bank->name) : 'ثبت نشده'}}</span>
        به نام {{is_null($profile->customer) ? 'ثب نشده' : (($profile->customer->gender==='male') ? 'آقای' : 'خانم')}}
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</span> فرزند <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->father}}</span> با کد ملی
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : toPersianNumbers($profile->customer->national_code)}}</span> که از این
        پس بعنوان پذیرنده شناخته می شود، مطابق جدول زیر
        واگذار گردید. این گواهی جهت تایید تحویل محصول و به عنوان سند دستگاه به نامبرده ارائه شده و فاقد هر گونه
        اعتبار دیگری می باشد.
    </div>
    <div>
        <table class="items">
            <tr>
                <td>مبلغ پرداختی بابت دستگاه (تومان)</td>
                <td>{{toPersianNumbers($terminal->device_amount)}}</td>
                <td>فیزیک دستگاه</td>
                <td>{{$terminal->devicePhysicalStatusText}}</td>
                <td>نام فروشگاه</td>
                <td>{{is_null($profile->business) ? 'ثب نشده' : $profile->business->name}}</td>
            </tr>
            <tr>
                <td>کد پایانه</td>
                <td>{{toPersianNumbers($terminal->terminal_number)}}</td>
                <td>نوع قرارداد</td>
                <td>{{$profile->typeText}}</td>
                <td>سریال دستگاه</td>
                <td>{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->serial) : 'ثبت نشده'}}</td>
            </tr>
            <tr>
                <td>سرویس دهنده</td>
                <td colspan="5">{{is_null($profile->psp) ? 'ثب نشده' : $profile->psp->name}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: right">
                    مدت گارانتی از تاریخ <span
                        class="variables">{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->guarantee_start) : 'نامشخص'}}</span>
                    لغایت <span
                        class="variables">{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->guarantee_end) : 'نامشخص'}}</span>
                    است
                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: right">
                    توضیحات تکمیلی:
                </td>
            </tr>
        </table>
    </div>
    <div class="contents">
        <h4>نکات الزام آور زیر به پذیرنده ابلاغ شدو پذیرنده ضمن تایید و درک کلیه این نکات، موظف به رعایت تمامی موارد زیر
            گردیده است:</h4>
        <ul>
            <li>دستگاه طبق قوانین جاری بانک مرکزی جمهوری اسلامی ایران و شرکت شاپرک واگذار شده و در صورت هرگونه تغییر
                در خدمات پرداخت توسط نهادهای مذکور پذیرنده مکلف به رعایت و انجام آن الزامات می باشد و حق هرگونه اعتراضی
                را از خود ساقط کرده است.
            </li>
            <li> دستگاه فروخته شده بعد از 48 ساعت از زمان نصب تحت هیچ شرایطی پس گرفته و یا تعویض نمی شود.</li>
            <li>هر گونه سهل انگاری، استفاده خلاف قانون و خلاف الزامات شاپرک و یا سوء استفاده از دستگاه مذکور بر عهده
                پذیرنده فوق الذکر خواهد بود و این شرکت مسئولیتی ندارد.
            </li>
            <li>تامین کاغذ رول و سیمکارت بر عهده پذیرنده است.</li>
            <li>فرم ها و مدارک مورد نیاز درخواست کارتخوان پس از نصب، انصراف و یا عودت دستگاه در محل شرکت بایگانی شده
                مسترد نمی گردد.
            </li>
            <li>در صورت خرابی دستگاه پذیرنده می بایست به محل نمایندگی مجاز شرکت مراجعه نماید.</li>
            <li>دستگاه بدون این سند فاقد اعتبار خواهد بود و شرکت هیچگونه خدمات گارانتی، تعویض و یا تغییر مالکیت را
                انجام نمی دهد.
            </li>
            <li>این دستگاه فقط جهت استفاده در کشور ایران واگذار شده است و درصورت بکارگیری آن در خارج از مرز جمهوری
                اسلامی ایران تمام عواقب قانونی و مدنی آن تماما بر عهده پذیرنده خواهد بود و پذیرنده اقرار می نماید که در
                این خصوص مطلع شده است و تایید می نماید.
            </li>
            <li>گارانتی دستگاه مربوط به ایرادات نرم افزاری است.</li>
            <li>هرگونه تغییر شماره حساب و یا واگذاری دستگاه به شخص ثالث، شامل هزینه عملیاتی است و پذیرنده موظف به
                پرداخت آن مطابق با قیمت همان روز خواهد بود.
            </li>
        </ul>
    </div>
    <div class="contents">
        <h4>موارد زیر موجب ابطال گارانتی دستگاه می گردد و پذیرنده اقرار می نماید که در این خصوص توجیح و مطلع شده
            است:</h4>
        <ul>
            <li>خاموش بودن دستگاه</li>
            <li> شکستگی دستگاه و تجهیزات آن.</li>
            <li>مفقود شدن دستگاه و ملحقات آن.</li>
            <li>ریختن آب یا هرگونه موادی که باعث آسیب رسانی ظاهری و یا خرابی سخت افزار دستگاه گردد.</li>
            <li>هرگونه دستکاری و تعمیر دستگاه خارج از نمایندگی مجاز شرکت.</li>
            <li>خرابی باطری و شارژر و سیم برق و سیم مبدل آن.</li>
            <li>افتادن دستگاه از ارتفاع و ضربه سخت</li>
            <li>دستگاههایی که یکبار تعویض شده اند و یا تغییر مالکیت هستند شامل گارانتی نمی گردد.</li>
        </ul>
    </div>
    <div class="contents">
        اینجانب <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</span> بعنوان پذیرنده و
        با کد ملی
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : toPersianNumbers($profile->customer->national_code)}}</span> ضمن مطالعه
        و دقت در تمامی موارد ذکر شده و تست دستگاه و پس از حصول اطمینان از سالم بودن
        و عملکرد صحیح آن، دستگاه مذکور را در مورخه <span
            class="variables">{{toPersianNumbers($profile->jUpdatedAt)}}</span> تحویل گرفتم.
    </div>
    <div>
        <table class="signs">
            <tr>
                <td>
                    <p>{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</p>
                    <p>امضا و اثر انگشت</p>
                </td>
                <td>
                    <p>مهر و امضاء شرکت</p>
                </td>
                <td>
                    <p>{{$profile->user->name}}</p>
                    <p>مهر و امضاء</p>
                </td>
            </tr>
        </table>
    </div>
</div>
<pagebreak></pagebreak>
<div id="container">
    <div class="pre-title">بسمه تعالی</div>
    <div class="title">رسید نصب و تحویل دستگاه کارتخوان – نسخه شرکت</div>
    <div class="contents">
        گواهی می شود یک عدد دستگاه کارتخوان مدل <span
            class="variables">{{is_null($terminal->deviceType) ? 'ثبت نشده' : $terminal->deviceType->name}}</span> به صورت
        <span
            class="variables">{{$terminal->deviceSellTypeText}}</span> و متصل
        به حساب بانک <span
            class="variables">{{(!is_null($profile->accounts) && count($profile->accounts) > 0 && !is_null($profile->accounts->first()->account)) ? toPersianNumbers($profile->accounts->first()->account->bank->name) : 'ثبت نشده'}}</span>
        به نام {{is_null($profile->customer) ? 'ثب نشده' : (($profile->customer->gender==='male') ? 'آقای' : 'خانم')}}
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</span> فرزند <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->father}}</span> با کد ملی
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : toPersianNumbers($profile->customer->national_code)}}</span> که از این
        پس بعنوان پذیرنده شناخته می شود، مطابق جدول زیر
        واگذار گردید. این گواهی جهت تایید تحویل محصول و به عنوان سند دستگاه به نامبرده ارائه شده و فاقد هر گونه
        اعتبار دیگری می باشد.
    </div>
    <div>
        <table class="items">
            <tr>
                <td>مبلغ پرداختی بابت دستگاه (تومان)</td>
                <td>{{toPersianNumbers($terminal->device_amount)}}</td>
                <td>فیزیک دستگاه</td>
                <td>{{$terminal->devicePhysicalStatusText}}</td>
                <td>نام فروشگاه</td>
                <td>{{is_null($profile->business) ? 'ثب نشده' : $profile->business->name}}</td>
            </tr>
            <tr>
                <td>کد پایانه</td>
                <td>{{toPersianNumbers($terminal->terminal_number)}}</td>
                <td>نوع قرارداد</td>
                <td>{{$profile->typeText}}</td>
                <td>سریال دستگاه</td>
                <td>{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->serial) : 'ثبت نشده'}}</td>
            </tr>
            <tr>
                <td>سرویس دهنده</td>
                <td colspan="5">{{is_null($profile->psp) ? 'ثب نشده' : $profile->psp->name}}</td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: right">
                    مدت گارانتی از تاریخ <span
                        class="variables">{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->guarantee_start) : 'نامشخص'}}</span>
                    لغایت <span
                        class="variables">{{!is_null($terminal->device) ? toPersianNumbers($terminal->device->guarantee_end) : 'نامشخص'}}</span>
                    است
                </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: right">
                    توضیحات تکمیلی:
                </td>
            </tr>
        </table>
    </div>
    <div class="contents">
        <h4>نکات الزام آور زیر به پذیرنده ابلاغ شدو پذیرنده ضمن تایید و درک کلیه این نکات، موظف به رعایت تمامی موارد زیر
            گردیده است:</h4>
        <ul>
            <li>دستگاه طبق قوانین جاری بانک مرکزی جمهوری اسلامی ایران و شرکت شاپرک واگذار شده و در صورت هرگونه تغییر
                در خدمات پرداخت توسط نهادهای مذکور پذیرنده مکلف به رعایت و انجام آن الزامات می باشد و حق هرگونه اعتراضی
                را از خود ساقط کرده است.
            </li>
            <li> دستگاه فروخته شده بعد از 48 ساعت از زمان نصب تحت هیچ شرایطی پس گرفته و یا تعویض نمی شود.</li>
            <li>هر گونه سهل انگاری، استفاده خلاف قانون و خلاف الزامات شاپرک و یا سوء استفاده از دستگاه مذکور بر عهده
                پذیرنده فوق الذکر خواهد بود و این شرکت مسئولیتی ندارد.
            </li>
            <li>تامین کاغذ رول و سیمکارت بر عهده پذیرنده است.</li>
            <li>فرم ها و مدارک مورد نیاز درخواست کارتخوان پس از نصب، انصراف و یا عودت دستگاه در محل شرکت بایگانی شده
                مسترد نمی گردد.
            </li>
            <li>در صورت خرابی دستگاه پذیرنده می بایست به محل نمایندگی مجاز شرکت مراجعه نماید.</li>
            <li>دستگاه بدون این سند فاقد اعتبار خواهد بود و شرکت هیچگونه خدمات گارانتی، تعویض و یا تغییر مالکیت را
                انجام نمی دهد.
            </li>
            <li>این دستگاه فقط جهت استفاده در کشور ایران واگذار شده است و درصورت بکارگیری آن در خارج از مرز جمهوری
                اسلامی ایران تمام عواقب قانونی و مدنی آن تماما بر عهده پذیرنده خواهد بود و پذیرنده اقرار می نماید که در
                این خصوص مطلع شده است و تایید می نماید.
            </li>
            <li>گارانتی دستگاه مربوط به ایرادات نرم افزاری است.</li>
            <li>هرگونه تغییر شماره حساب و یا واگذاری دستگاه به شخص ثالث، شامل هزینه عملیاتی است و پذیرنده موظف به
                پرداخت آن مطابق با قیمت همان روز خواهد بود.
            </li>
        </ul>
    </div>
    <div class="contents">
        <h4>موارد زیر موجب ابطال گارانتی دستگاه می گردد و پذیرنده اقرار می نماید که در این خصوص توجیح و مطلع شده
            است:</h4>
        <ul>
            <li>خاموش بودن دستگاه</li>
            <li> شکستگی دستگاه و تجهیزات آن.</li>
            <li>مفقود شدن دستگاه و ملحقات آن.</li>
            <li>ریختن آب یا هرگونه موادی که باعث آسیب رسانی ظاهری و یا خرابی سخت افزار دستگاه گردد.</li>
            <li>هرگونه دستکاری و تعمیر دستگاه خارج از نمایندگی مجاز شرکت.</li>
            <li>خرابی باطری و شارژر و سیم برق و سیم مبدل آن.</li>
            <li>افتادن دستگاه از ارتفاع و ضربه سخت</li>
            <li>دستگاههایی که یکبار تعویض شده اند و یا تغییر مالکیت هستند شامل گارانتی نمی گردد.</li>
        </ul>
    </div>
    <div class="contents">
        اینجانب <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</span> بعنوان پذیرنده و
        با کد ملی
        <span
            class="variables">{{is_null($profile->customer) ? 'ثب نشده' : toPersianNumbers($profile->customer->national_code)}}</span> ضمن مطالعه
        و دقت در تمامی موارد ذکر شده و تست دستگاه و پس از حصول اطمینان از سالم بودن
        و عملکرد صحیح آن، دستگاه مذکور را در مورخه <span
            class="variables">{{toPersianNumbers($profile->jUpdatedAt)}}</span> تحویل گرفتم.
    </div>
    <div>
        <table class="signs">
            <tr>
                <td>
                    <p>{{is_null($profile->customer) ? 'ثب نشده' : $profile->customer->fullName}}</p>
                    <p>امضا و اثر انگشت</p>
                </td>
                <td>
                    <p>مهر و امضاء شرکت</p>
                </td>
                <td>
                    <p>{{$profile->user->name}}</p>
                    <p>مهر و امضاء</p>
                </td>
            </tr>
        </table>
    </div>
</div>
