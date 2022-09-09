<?php

namespace App\Imports\Profiles\Psp;

use App\Models\Profiles\Account;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Bank;
use App\Models\Variables\BusinessSubCategory;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;
use Morilog\Jalali\Jalalian;

class Pasargad implements OnEachRow, WithStartRow
{
    const USER_ID = 2;
    const PROFILE_TYPE = 'REGISTER';
    const PSP_ID = 4;

    private Profile $profile;

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $profile = Profile::with('accounts')
            ->with('accounts.account')
            ->where('psp_id', self::PSP_ID)
            ->where(function ($query) use ($row) {
                $merchantCode = $row[1];
                $merchantId = $row[1];
                if (substr($merchantCode, 0, 1) == '9') {
                    $merchantId = substr($merchantCode, 1);
                }
                $query->where('merchant_id', $merchantId);
            })
            ->get()
            ->first();
        if (is_null($row[1]) || is_null($profile)) {
            $this->setProfile($row);
            $this->setCustomer($row);
            $this->setBusiness($row);
            $this->setAccount($row);
            $this->setTerminal($row);
        } else {
            $this->profile = $profile;
            $terminal = Terminal::where(function ($query) use ($row) {
                $terminalCode = $row[0];
                $terminalId = $row[0];
                if (substr($terminalCode, 0, 1) == '9') {
                    $terminalId = substr($terminalCode, 1);
                }
                $query->where('terminal_number', $terminalId)->orWhere('terminal_number', $terminalCode);
            })
                ->get()
                ->first();
            if (is_null($terminal)) {
                if (!is_null($row[21]) && count($profile->accounts) > 0 && !is_null($profile->accounts->first()->account) && $row[21] != $profile->accounts->first()->account->account_number) {
                    $this->setAccount($row);
                }
                $this->setTerminal($row);
            }
        }
    }

    private function setProfile(array $row)
    {
        switch ($row[4]) {
            default:
            case 'ترمینال نصب شده':
                $status = 8;
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

        if (!is_null($row[18])) $this->profile->created_at = Jalalian::fromFormat('Y/m/d', substr($row[18], 0, 10))->toCarbon();
        if (!is_null($row[15])) $this->profile->updated_at = Jalalian::fromFormat('Y/m/d', substr($row[15], 0, 10))->toCarbon();
        $this->profile->save();
    }

    private function setCustomer(array $row)
    {
        $firstName = 'نامشخص';
        $lastName = 'نامشخص';
        if (!is_null($row[5])) {
            $name = explode(' ', $row[5]);
            $firstName = $name[0];
            $lastName = '';
            for ($i = 1; $i < count($name); $i++) {
                $lastName .= ' ' . $name[$i];
            }
        } elseif (!is_null($row[3])) {
            $name = explode(' ', $row[3]);
            $firstName = $name[0];
            $lastName = '';
            for ($i = 1; $i < count($name); $i++) {
                $lastName .= ' ' . $name[$i];
            }
        }

        $type = is_null($row[24]) ? 'ORGANIZATION' : 'PERSON';
        $this->profile->customer()->create([
            'type' => $type,
            'user_id' => self::USER_ID,
            'national_code' => is_null($row[24]) ? '-' : $row[24],
            'id_code' => is_null($row[24]) ? '-' : $row[24],
            'first_name' => $firstName,
            'first_name_english' => null,
            'last_name' => $lastName,
            'last_name_english' => null,
            'father' => null,
            'father_english' => null,
            'gender' => 'male',
            'mobile' => !is_null($row[12]) ? str_replace('-', '', $row[12]) : '-',
            'birthday' => '2020-01-01',
            'company_name' => $type === 'ORGANIZATION' ? $row[3] : null,
            'company_name_english' => null,
            'business_name' => null,
            'reg_date' => null,
            'reg_code' => null,
            'company_national_code' => is_null($row[25]) ? '-' : $row[25],
        ]);
    }

    private function setBusiness(array $row)
    {
        $ostan = DB::table('ostan')->where('name', $row[20])->select()->get(['id', 'name'])->first();
        $city = DB::table('shahr')->where('name', $row[19])->select()->get(['id', 'name'])->first();


        $phoneCode = null;
        $phone = null;

        if (!is_null($row[11])) {
            $phoneData = explode('-', $row[11]);
            if ($phoneData) {
                $phoneCode = $phoneData[0];
                $phone = $phoneData[1];
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
            'phone_code' => $phoneCode,
            'senf' => $row[7],
            'name' => $row[3],
            'name_english' => null,
            'address' => $row[10],
            'postal_code' => null,
            'phone' => $phone,
            'tax_code' => null,
            'has_license' => 'NO',
            'license_code' => null,
            'license_date' => null
        ]);
    }

    private function setAccount(array $row)
    {
        $bank = Bank::where('name', $row[14])->get()->first();

        $account = Account::create([
            'customer_id' => $this->profile->customer->id,
            'bank_id' => is_null($bank) ? 34 : $bank->id,
            'branch' => $row[23],
            'account_number' => is_null($row[21]) ? 'نامشخص' : str_replace(['-', '_', '.', ' '], '', $row[21]),
            'sheba_code' => is_null($row[22]) ? 'نامشخص' : str_replace(['-', '_', '.', ' '], '', $row[22]),
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
        if (!is_null($row[13])) {
            $deviceConnectionType = DeviceConnectionType::where('name', $row[13])->get()->first();
            $deviceType = DeviceType::where('device_connection_type_id', $deviceConnectionType->id)->get()->first();
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
        }
        $deviceId = null;
        if (!is_null($row[6])) {
            $device = Device::firstOrCreate([
                'serial' => $row[6]
            ], [
                'user_id' => self::USER_ID,
                'device_type_id' => $deviceTypeId,
                'guarantee_start' => Jalalian::now()->format('Y-m-d'),
                'guarantee_end' => Jalalian::now()->format('Y-m-d'),
                'description' => sprintf('اضافه شده توسط فایل اکسل پاسارگاد مورخ %s', Jalalian::now()->format('Y/m/d')),
                'status' => 2
            ]);

            $deviceId = $device->id;
            if ($device->transport_status === 1 && $device->psp_status === 1) {
                $device->transport_status = 3;
                $device->psp_status = 2;
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
