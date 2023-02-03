<?php

namespace App\Jobs\Profiles;

use App\Exports\Profiles\ProfileExport;
use App\Models\Profiles\Profile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use ZipArchive;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExportProfiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $profiles;
    private User $user;
    private int $maxAccountsCount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Collection $profiles, User $user)
    {
        $this->profiles = $profiles;
        $this->maxAccountsCount = $profiles->max('accounts_count');
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        Cache::put(sprintf('%s.profiles.export.status', $this->user->id), 'processing');
        $jDate = Jalalian::forge(now())->format('Y.m.d');
        $directoryName = $jDate;
        Cache::put(sprintf('%s.profiles.export.directory', $this->user->id), $directoryName);
        $fileName = 'profiles.' . $jDate . '_' . time() . '.xlsx';
//        Excel::store(new ProfileExport($this->profiles), 'temp/excel/profiles/' . $directoryName . '/' . $fileName);
        $fullPath = storage_path(sprintf('app/temp/excel/profiles/%s/%s', $directoryName, $fileName));
        Storage::copy('temp/excel/Template.xlsx', sprintf('temp/excel/profiles/%s/%s', $directoryName, $fileName));
        Log::channel('daily')->info($fullPath);
        $writer = SimpleExcelWriter::create($fullPath);
        foreach ($this->collection() as $item) {
            Log::channel('daily')->info($item['id']);
            $writer->addRow($item);
        }
        Log::channel('daily')->info($fullPath);

        $done = Cache::get(sprintf('%s.profiles.export.done', $this->user->id));
        if (is_null($done)) {
            Cache::put(sprintf('%s.profiles.export.done', $this->user->id), 1);
        } else {
            Cache::increment(sprintf('%s.profiles.export.done', $this->user->id));
        }
    }

    public function collection(): array
    {

        $list = collect();
        /**
         * @var Profile $profile
         */
        $j = 1;
        $start = $j;
        foreach ($this->profiles as $profile) {
            $profileData = [
                $profile->id,
                substr($profile->jCreatedAt, 0, 10),
                substr($profile->jUpdatedAt, 0, 10),
                $profile->user ? $profile->user->name : '',
                $profile->statusText,
                //اطلاعات سرویس دهنده و دستگاه
                $profile->psp ? $profile->psp->name : '',
                $profile->merchant_id,
                //اطلاعات اولین ترمینال
                $profile->terminals->count() > 0 ? $profile->terminals->first()->terminal_id : '',
                $profile->terminals->count() > 0 ? $profile->terminals->first()->deviceType ? $profile->terminals->first()->deviceType->name : '' : '',
                $profile->terminals->count() > 0 ? (is_null($profile->terminals->first()->device) ? null : $profile->terminals->first()->device->serial) : '',
                $profile->terminals->count() > 0 ? (is_null($profile->terminals->first()->device) ? null : $profile->terminals->first()->device->imei) : '',
                $profile->terminals->count() > 0 ? (is_null($profile->terminals->first()->device) ? null : $profile->terminals->first()->device->sim_number) : '',
                $profile->terminals->count() > 0 ? $profile->terminals->first()->deviceSellTypeText : '',
                $profile->terminals->count() > 0 ? $profile->terminals->first()->device_amount : '',
                $profile->terminals->count() > 0 ? $profile->terminals->first()->device_dept_profile_id : '',
                $profile->terminals->count() > 0 ? $profile->terminals->first()->devicePhysicalStatusText : '',
            ];
            $customerData = [
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
            ];
            $businessData = [];
            if ($profile->business) {
                $businessData = [
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
                    $profile->business->tax_code,
                    ($profile->business->has_license == 'YES' ? 'بله' : 'خیر'),
                    $profile->business->license_code,
                    $profile->business->jLicenseDate,
                ];
            }
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

            $x = array_merge($profileData, $customerData, $businessData, $accounts->toArray());
            $list->push($x);
            for ($i = 1; $i < $profile->terminals->count(); $i++) {
                $list->push([
                    '', '', '', '', '', '', '',
                    $profile->terminals[$i]->terminal_id,
                    $profile->terminals[$i]->deviceType ? $profile->terminals[$i]->deviceType->name : '',
                    (is_null($profile->terminals[$i]->device) ? null : $profile->terminals[$i]->device->serial),
                    (is_null($profile->terminals[$i]->device) ? null : $profile->terminals[$i]->device->imei),
                    (is_null($profile->terminals[$i]->device) ? null : $profile->terminals[$i]->device->sim_number),
                    $profile->terminals[$i]->deviceSellTypeText,
                    $profile->terminals[$i]->device_amount,
                    $profile->terminals[$i]->device_dept_profile_id,
                    $profile->terminals[$i]->devicePhysicalStatusText,
                ]);
                $j++;
            }
            $this->mergeList[] = ['start' => $start, 'end' => $start + $profile->terminals->count()];
            $j++;
        }

        return $list->toArray();
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
            $accountsHeading[] = array_merge($accountsHeading, $accounts);
        }
        $headings = [
            'شناسه پرونده',
            'تاریخ ثبت اولیه',
            'تاریخ تایید نهایی',
            'کاربر ثبت کننده',
            'وضعیت پرونده',
            'سرویس دهنده',
            'شماره پذیرنده',
            'شماره پایانه',
            'مدل دستگاه',
            'سریال',
            'کد IMEI',
            'شماره سیم‌کارت',
            'شیوه فروش',
            'مبلغ فروش / امانت / قسط',
            'شماره پرونده اقساط',
            'وضعیت فیزیک دستگاه',
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
            'کد مالیاتی',
            'دارای جواز کسب',
            'شماره جواز',
            'تاریخ جواز',
        ];
        return array_merge($headings, $accountsHeading);
    }
}
