<?php

namespace App\Exports\Profiles;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProfileExport implements FromCollection, WithHeadings
{
    private $collection;
    private $maxAccountsCount;

    /**
     * DeviceExport constructor.
     * @param $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->maxAccountsCount = $collection->max('accounts_count');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $list = collect();
        foreach ($this->collection as $profile) {
            $item = collect();
            $accounts = collect();
            foreach ($profile->accounts as $account) {
                $accounts->push(
                    $account->account->bank->name,
                    $account->account->branch,
                    $account->account->account_number,
                    $account->account->shebaText,
                    $account->account->first_name,
                    $account->account->last_name,
                    $account->account->national_code,
                    $account->account->mobile,
                    $account->account->jBirthday,
                );
            }

            $item->push(
                substr($profile->jCreatedAt, 0, 10),
                substr($profile->jUpdatedAt, 0, 10),
                $profile->user ? $profile->user->name : '',
                $profile->statusText,
                //اطلاعات سرویس دهنده و دستگاه
                $profile->psp ? $profile->psp->name : '',
                $profile->deviceType ? $profile->deviceType->name : '',
                (is_null($profile->device) ? null : $profile->device->serial),
                $profile->terminal_id,
                $profile->merchant_id,
                //اطلاعات مشتری
                $profile->customer->typeText,
                $profile->customer->first_name,
                $profile->customer->first_name_english,
                $profile->customer->last_name,
                $profile->customer->last_name_english,
                $profile->customer->father,
                $profile->customer->father_english,
                $profile->customer->national_code,
                $profile->customer->id_code,
                $profile->customer->genderText,
                $profile->customer->mobile,
                $profile->customer->jBirthday,
                $profile->customer->company_name,
                $profile->customer->company_name_english,
                $profile->customer->business_name,
                $profile->customer->jRegDate,
                $profile->customer->reg_code,
                $profile->customer->company_national_code,
            );
            //اطلاعات کسب و کار
            if ($profile->business) {
                $business = collect([
                    $profile->business->ostan,
                    $profile->business->shahrestan,
                    $profile->business->bakhsh,
                    $profile->business->shahr,
                    $profile->business->fullPhone,
                    $profile->business->senf,
                    $profile->business ? $profile->business->name : '',
                    $profile->business->name_english,
                    $profile->business->address,
                    $profile->business->postal_code,
                    ($profile->business->has_license == 'YES' ? 'بله' : 'خیر'),
                    $profile->business->license_code,
                    $profile->business->jLicenseDate,
                ]);
                $item = $item->merge($business);
            }
            $list->push($item->merge($accounts));
        }

        return $list;
    }

    public function headings(): array
    {
        $accountsHeading = [];
        for ($i = 0; $i < $this->maxAccountsCount; $i++) {
            $accounts = [
                'نام بانک',
                'کد شعبه',
                'شماره حساب',
                'شماره شبا',
                'نام صاحب حساب',
                'نام خانوادگی صاحب حساب',
                'کد ملی',
                'تلفن تماس',
                'تاریخ تولد',
            ];
            $accountsHeading = array_merge($accountsHeading, $accounts);
        }
        $headings = [
            'تاریخ ثبت اولیه',
            'تاریخ تایید نهایی',
            'کاربر ثبت کننده',
            'وضعیت پرونده',
            'سرویس دهنده',
            'مدل دستگاه',
            'سریال',
            'شماره پایانه',
            'شماره پذیرنده',
            'نوع پذیرنده',
            'نام',
            'نام انگلیسی',
            'نام خانوادگی',
            'نام خانوادگی انگلیسی',
            'نام پدر',
            'نام پدر انگلیسی',
            'کد ملی',
            'شماره شناسنامه',
            'جنسیت',
            'تلفن همراه',
            'تاریخ تولد',
            'نام شرکت',
            'نام شرکت انگلیسی',
            'نام تجاری',
            'تاریخ ثبت',
            'شماره ثبت',
            'شناسه ملی',
            'استان',
            'شهرستان',
            'بخش',
            'شهر',
            'تلفن تماس',
            'صنف مرتبط',
            'نام کسب و کار',
            'نام کسب و کار انگلیسی',
            'آدرس',
            'کد پستی',
            'دارای جواز کسب',
            'شماره جواز',
            'تاریخ جواز',
        ];
        return array_merge($headings, $accountsHeading);
    }
}
