<?php

namespace App\Imports\Profiles\Psp;

use App\Models\Profiles\Account;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Bank;
use App\Models\Variables\BusinessCategory;
use App\Models\Variables\BusinessSubCategory;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;
use Morilog\Jalali\Jalalian;

class Sadad implements OnEachRow, WithStartRow
{

    const USER_ID = 2;
    const PROFILE_TYPE = 'REGISTER';
    const PSP_ID = 8;

    private Profile $profile;

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $profile = Profile::where('merchant_id', $row[0])
            ->where('psp_id', self::PSP_ID)
            ->get()
            ->first();
        if (is_null($row[0]) || is_null($profile)) {
            $this->setProfile($row);
            $this->setCustomer($row);
            $this->setBusiness($row);
            $this->setAccount($row);
            $this->setTerminal($row);
        } else {
            $this->profile = $profile;
            $terminal = Terminal::where('profile_id', $profile->id)->where('terminal_number', $row[6])->get()->first();
            if (is_null($terminal)) {
                $this->setTerminal($row);
            }
        }
    }

    public function startRow(): int
    {
        return 3;
    }

    private function setProfile(array $row)
    {
        switch ($row[7]) {
            default:
            case 'راه اندازی شده':
                $status = 8;
                break;
            case 'غیر فعال سازی موقت':
                $status = 254;
                break;
            case 'ترمینال منتظر تخصیص':
            case 'تخصیص به پذیرنده':
            case 'ترمینال منتظر نصب':
                $status = 7;
                break;
            case 'ترمینال منتظر انصراف':
                $status = 12;
                break;
            case 'انصراف از نصب':
            case 'غیر فعال سازی':
            case 'فسخ':
                $status = 9;
                break;
        }
        $this->profile = Profile::create([
            'type' => self::PROFILE_TYPE,
            'user_id' => self::USER_ID,
            'psp_id' => self::PSP_ID,
            'merchant_id' => $row[0],
            'status' => $status,
            'licenses_status' => 0,
            'device_physical_status' => 'new',
        ]);


        if (!is_null($row[21])) $this->profile->created_at = Jalalian::fromFormat('Y/m/d', substr($row[21], 0, 10))->toCarbon();
        if (!is_null($row[8])) $this->profile->updated_at = Jalalian::fromFormat('Y/m/d', substr($row[8], 0, 10))->toCarbon();
        $this->profile->save();
    }

    private function setCustomer(array $row)
    {
        $firstName = $row[15];
        $lastName = $row[16];
        $type = 'PERSON';
        if (is_null($row[16])) {
            $type = 'ORGANIZATION';
        }
        $this->profile->customer()->create([
            'type' => $type,
            'user_id' => self::USER_ID,
            'national_code' => is_null($row[17]) ? 'نامشخص' : $row[17],
            'id_code' => is_null($row[17]) ? 'نامشخص' : $row[17],
            'first_name' => $this->manipulateCustomerName($firstName),
            'first_name_english' => Str::slug($firstName),
            'last_name' => is_null($lastName) ? 'نامشخص' : $this->manipulateCustomerName($lastName),
            'last_name_english' => is_null($lastName) ? null : Str::slug($lastName),
            'father' => null,
            'father_english' => null,
            'gender' => 'male',
            'mobile' => substr($row[18], 0, 1) == '0' ? str_replace(['-', ' ', '_'], '', $row[18]) : sprintf('0%s', str_replace(['-', ' ', '_'], '', $row[18])),
            'birthday' => '2020-01-01',
            'company_name' => $type === 'ORGANIZATION' ? $firstName : null,
            'company_name_english' => Str::slug($firstName),
            'business_name' => null,
            'reg_date' => null,
            'reg_code' => null,
            'company_national_code' => $type === 'ORGANIZATION' ? $row[17] : null
        ]);
    }

    private function setBusiness(array $row)
    {
        $ostan = DB::table('ostan')->where('name', $row[24])->select()->get(['id', 'name'])->first();
        $city = DB::table('shahr')->where('name', $row[25])->select()->get(['id', 'name'])->first();

        $businessCategoryCodeId = null;
        $businessSubCategoryCodeId = null;
        if (!is_null($row[30])) {
            $businessCategory = BusinessCategory::where('code', $row[30])->get()->first();
            if (!is_null($businessCategory)) {
                $businessCategoryCodeId = $businessCategory->id;
                $businessSubCategory = BusinessSubCategory::where('category_id', $businessCategoryCodeId)->where('code', $row[31])->get()->first();
                if (!is_null($businessSubCategory)) {
                    $businessSubCategoryCodeId = $businessSubCategory->id;
                }
            }
        }

        $this->profile->business()->create([
            'profile_id' => $this->profile->id,
            'ostan_id' => is_null($ostan) ? 22 : $ostan->id,
            'shahrestan_id' => null,
            'bakhsh_id' => null,
            'shahr_id' => is_null($city) ? 1142 : $city->id,
            'dehestan_id' => null,
            'abadi_id' => null,
            'phone_code' => $row[26],
            'business_category_code' => $businessCategoryCodeId,
            'business_subCategory_code' => $businessSubCategoryCodeId,
            'name' => $row[1],
            'name_english' => Str::slug($row[1]),
            'address' => $row[27],
            'postal_code' => null,
            'phone' => substr($row[28], 3),
            'tax_code' => null,
            'has_license' => 'NO',
            'license_code' => null,
            'license_date' => null
        ]);
    }

    private function setAccount(array $row)
    {
        $account = Account::create([
            'customer_id' => $this->profile->customer->id,
            'bank_id' => 34,
            'branch' => 'نامشخص',
            'account_number' => 'نامشخص',
            'sheba_code' => 'نامشخص',
            'first_name' => $this->profile->customer->first_name,
            'last_name' => $this->profile->customer->last_name,
            'national_code' => $this->profile->customer->national_code,
            'mobile' => $this->profile->customer->mobile,
            'birthday' => $this->profile->customer->birthday
        ]);

        ProfilesAccount::create([
            'profile_id' => $this->profile->id,
            'account_id' => $account->id
        ]);
    }

    private function setTerminal(array $row)
    {
        $deviceTypeId = 48;
        $deviceConnectionTypeId = 2;
        if (!is_null($row[5])) {
            $deviceType = DeviceType::firstOrCreate(
                ['name' => strtoupper($row[5])],
                [
                    'device_connection_type_id' => 2,
                    'status' => 1
                ]
            );
            $deviceTypeId = $deviceType->id;
            $deviceConnectionTypeId = $deviceType->device_connection_type_id;
        }
        switch ($row[7]) {
            default:
            case 'راه اندازی شده':
                $status = 6;
                break;
            case 'غیر فعال سازی موقت':
            case 'نامشخص':
                $status = 254;
                break;
            case 'ترمینال منتظر تخصیص':
            case 'تخصیص به پذیرنده':
            case 'ترمینال منتظر نصب':
                $status = 3;
                break;
            case 'ترمینال منتظر انصراف':
                $status = 4;
                break;
            case 'انصراف از نصب':
            case 'غیر فعال سازی':
            case 'فسخ':
                $status = 5;
                break;
        }
        $deviceId = null;
        if (!is_null($row[4])) {
            $device = Device::firstOrCreate([
                'serial' => $row[4],
                'device_type_id' => $deviceTypeId,
            ], [
                'user_id' => self::USER_ID,
                'device_type_id' => $deviceTypeId,
                'guarantee_start' => now()->format('Y-m-d'),
                'guarantee_end' => now()->format('Y-m-d'),
                'description' => sprintf('اضافه شده توسط فایل اکسل مورخ %s', Jalalian::now()->format('Y/m/d')),
                'status' => 2
            ]);

            $deviceId = $device->id;
            if ($device->transport_status === 1 && $device->psp_status === 1) {
                if ($status === 6) {
                    $device->transport_status = 3;
                    $device->psp_status = 2;
                } elseif ($status === 3) {
                    $device->transport_status = 2;
                    $device->psp_status = 2;
                }
                $device->save();
            }
        }
        $this->profile->terminals()->create([
            'terminal_number' => $row[6],
            'device_type_id' => $deviceTypeId,
            'device_connection_type_id' => $deviceConnectionTypeId,
            'device_id' => $deviceId,
            'status' => $status,
        ]);
    }

    private function manipulateCustomerName(string $name)
    {
        $name = str_replace('ي', 'ی', $name);
        $name = str_replace('ك', 'ک', $name);
        $name = trim($name);

        return $name;
    }

}
