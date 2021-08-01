<?php

namespace App\Imports\Profiles;

use App\Models\Profiles\Account;
use App\Models\Profiles\Business;
use App\Models\Profiles\Customer;
use App\Models\Profiles\Profile;
use App\Models\Variables\Bank;
use App\Models\Variables\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CustomImport implements ToModel, WithStartRow
{

    private Profile $profile;
    private Customer $customer;

    public function model(array $row)
    {
        $this->createProfile($row);
        $this->createCustomer($row);
        $this->createBusiness($row);
        $this->createAccount($row);
    }

    public function startRow(): int
    {
        return 2;
    }

    private function createProfile(array $row)
    {
        $deviceId = null;
        if ($row[2] && !is_null($row[2])) {
            $device = Device::firstOrCreate([
                'device_type_id' => 1,
                'serial' => $row[2],
            ], [
                'user_id' => 2,
                'physical_status' => 1,
                'transport_status' => 3,
                'psp_status' => 2,
                'description' => 'ثبت شده از طریق فایل اکسل سرویس دهنده آیریک',
                'status' => 2
            ]);
            $deviceId = $device->id;
        }
        $profile = Profile::create([
            'type' => 'REGISTER',
            'user_id' => 2,
            'psp_id' => 14,
            'device_type_id' => 1,
            'device_id' => $deviceId,
            'multi_account' => 0,
            'terminal_id' => $row[3],
            'merchant_id' => $row[4],
            'status' => is_null($deviceId) ? 5 : 8,
            'device_sell_type' => 'cash',
        ]);

        $this->profile = $profile;
    }

    private function createCustomer(array $row)
    {
        $customer = Customer::create([
            'type' => 'PERSON',
            'user_id' => 2,
            'profile_id' => $this->profile->id,
            'national_code' => $row[12],
            'id_code' => $row[13],
            'first_name' => $row[6],
            'first_name_english' => $row[7],
            'last_name' => $row[8],
            'last_name_english' => $row[9],
            'father' => $row[10],
            'father_english' => $row[11],
            'gender' => null,
            'mobile' => $row[15],
            'birthday' => '2020-01-01',
            'company_name' => $row[17],
            'company_name_english' => $row[18],
            'business_name' => $row[19],
            'reg_date' => null,
            'reg_code' => null,
            'company_national_code' => $row[22]
        ]);
        $this->customer = $customer;
    }

    private function createBusiness(array $row)
    {
        $ostan = DB::table('ostan')->where('name', $row[23])->select()->get(['id', 'name'])->first();
        $city = DB::table('shahr')->where('name', $row[26])->select()->get(['id', 'name'])->first();
        $tell = $row[27];
        $code = 0;
        $phone = 0;
        $tellData = explode('-', $tell);
        if (count($tellData) == 2) {
            $code = $tellData[0];
            $phone = $tellData[1];
        }
        Business::create([
            'profile_id' => $this->profile->id,
            'ostan_id' => $ostan->id,
            'shahrestan_id' => null,
            'bakhsh_id' => null,
            'shahr_id' => $city->id,
            'dehestan_id' => null,
            'abadi_id' => null,
            'phone_code' => $code,
            'senf' => $row[28],
            'name' => $row[29],
            'name_english' => $row[30],
            'address' => $row[31],
            'postal_code' => $row[32],
            'phone' => $phone,
            'tax_code' => null,
            'has_license' => 'NO',
            'license_code' => null,
            'license_date' => null
        ]);
    }

    private function createAccount($row)
    {
        $bank = Bank::where('name', $row[33])->get()->first();
        Account::create([
            'customer_id' => $this->customer->id,
            'bank_id' => is_null($bank) ? null : $bank->id,
            'branch' => $row[34],
            'account_number' => $row[35],
            'sheba_code' => $row[36],
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'national_code' => $this->customer->national_code,
            'mobile' => $this->customer->mobile,
            'birthday' => $this->customer->birthday
        ]);
    }
}
