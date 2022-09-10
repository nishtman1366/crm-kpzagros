<?php

namespace App\Imports\Profiles\Psp;

use App\Imports\Profiles\CustomImport;
use App\Models\Profiles\Account;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Bank;
use App\Models\Variables\BusinessSubCategory;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;
use Morilog\Jalali\Jalalian;

class AirikPasargad implements OnEachRow, WithStartRow
{
    const USER_ID = 2;
    const PROFILE_TYPE = 'REGISTER';
    const PSP_ID = 14;

    private Profile $profile;

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $profile = Profile::with('accounts')
            ->with('accounts.account')
            ->where('psp_id',self::PSP_ID)
            ->where('merchant_id', $row[1])->get()->first();
        if (is_null($row[1]) || is_null($profile)) {
            $this->setProfile($row);
            $this->setCustomer($row);
            $this->setBusiness($row);
            $this->setAccount($row);
            $this->setTerminal($row);
        } else {
            $this->profile = $profile;
            $terminal = Terminal::where('terminal_number', $row[0])->get()->first();
            if (is_null($terminal)) {
                if (!is_null($row[3]) && count($profile->accounts) > 0 && !is_null($profile->accounts->first()->account) && $row[3] != $profile->accounts->first()->account->account_number) {
                    $this->setAccount($row);
                }
                $this->setTerminal($row);
            }
        }
    }

    private function setProfile(array $row)
    {
        switch ($row[20]) {
            default:
            case 'ترمینال نصب شده':
                $status = 8;
                break;
            case 'نامشخص':
                $status = 254;
                break;
            case 'ترمینال منتظر تخصیص':
            case 'ترمینال منتظر نصب':
                $status = 7;
                break;
            case 'ترمینال منتظر انصراف':
                $status = 12;
                break;
        }
        $this->profile = Profile::create([
            'type' => self::PROFILE_TYPE,
            'user_id' => self::USER_ID,
            'psp_id' => self::PSP_ID,
            'merchant_id' => $row[1],
            'status' => $status,
            'licenses_status' => 0,
            'device_physical_status' => 'new',
        ]);
    }

    private function setCustomer(array $row)
    {
        $firstName = 'نامشخص';
        $lastName = 'نامشخص';
        if (!is_null($row[4])) {
            $name = explode(' ', $row[4]);
            $firstName = $name[0];
            $lastName = '';
            for ($i = 1; $i < count($name); $i++) {
                $lastName .= ' ' . $name[$i];
            }
        }
        $this->profile->customer()->create([
            'type' => 'PERSON',
            'user_id' => self::USER_ID,
            'national_code' => is_null($row[5]) ? 'نامشخص' : $row[5],
            'id_code' => is_null($row[5]) ? 'نامشخص' : $row[5],
            'first_name' => $firstName,
            'first_name_english' => null,
            'last_name' => $lastName,
            'last_name_english' => null,
            'father' => null,
            'father_english' => null,
            'gender' => 'male',
            'mobile' => substr($row[7], 0, 1) == '0' ? str_replace(['-', ' ', '_'], '', $row[7]) : sprintf('0%s', str_replace(['-', ' ', '_'], '', $row[7])),
            'birthday' => '2020-01-01',
            'company_name' => null,
            'company_name_english' => null,
            'business_name' => null,
            'reg_date' => null,
            'reg_code' => null,
            'company_national_code' => null
        ]);
    }

    private function setBusiness(array $row)
    {
        $ostan = DB::table('ostan')->where('name', $row[11])->select()->get(['id', 'name'])->first();
        $city = DB::table('shahr')->where('name', $row[12])->select()->get(['id', 'name'])->first();

        $businessCategoryCodeId = null;
        $businessSubCategoryCodeId = null;
        if (!is_null($row[10])) {
            $businessSubCategory = BusinessSubCategory::where('code', $row[10])->get()->first();
            if (!is_null($businessSubCategory)) {
                $businessSubCategoryCodeId = $businessSubCategory->id;
                $businessCategoryCodeId = $businessSubCategory->category_id;
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
            'phone_code' => 0,
            'business_category_code' => $businessCategoryCodeId,
            'business_subCategory_code' => $businessSubCategoryCodeId,
            'name' => $row[8],
            'name_english' => null,
            'address' => $row[13],
            'postal_code' => null,
            'phone' => 0,
            'tax_code' => null,
            'has_license' => 'NO',
            'license_code' => null,
            'license_date' => null
        ]);
    }

//30094
    private function setAccount(array $row)
    {
        $bank = Bank::where('name', $row[15])->get()->first();

        $account = Account::create([
            'customer_id' => $this->profile->customer->id,
            'bank_id' => is_null($bank) ? 34 : $bank->id,
            'branch' => 'نامشخص',
            'account_number' => is_null($row[3]) ? 'نامشخص' : $row[3],
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
        if (!is_null($row[2])) {
            $deviceType = DeviceType::where('name', $row[2])->get()->first();
            if (!is_null($deviceType)) {
                $deviceTypeId = $deviceType->id;
                $deviceConnectionTypeId = $deviceType->device_connection_type_id;
            }
        }
        switch ($row[20]) {
            default:
            case 'ترمینال نصب شده':
                $status = 6;
                break;
            case 'نامشخص':
                $status = 254;
                break;
            case 'ترمینال منتظر تخصیص':
            case 'ترمینال منتظر نصب':
                $status = 3;
                break;
            case 'ترمینال منتظر انصراف':
                $status = 4;
                break;
        }
        $deviceId = null;
        if (!is_null($row[14])) {
            $device = Device::firstOrCreate([
                'serial' => $row[14]
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
                } elseif ($status === 1) {
                    $device->transport_status = 2;
                } elseif ($status === 3) {
                    $device->transport_status = 2;
                    $device->psp_status = 2;
                }
                $device->save();
            }
        }
        $this->profile->terminals()->create([
            'terminal_number' => $row[0],
            'device_type_id' => $deviceTypeId,
            'device_connection_type_id' => $deviceConnectionTypeId,
            'device_id' => $deviceId,
            'status' => $status,
        ]);
    }


    public function startRow(): int
    {
        return 3;
    }
}
