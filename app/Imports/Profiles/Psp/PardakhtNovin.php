<?php

namespace App\Imports\Profiles\Psp;

use App\Models\Profiles\Account;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Bank;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;
use Morilog\Jalali\Jalalian;
use Psy\Util\Str;

class PardakhtNovin implements OnEachRow, WithStartRow
{
    const USER_ID = 2;
    const PROFILE_TYPE = 'REGISTER';
    const PSP_ID = 6;

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $profile = Profile::with('accounts')
            ->with('accounts.account')
            ->where('psp_id', self::PSP_ID)
            ->where(function ($query) use ($row) {
                $query->where('merchant_id', $row[9])
                    ->orWhereHas('customer', function ($customerQuery) use ($row) {
                        $nationalCodeLength = strlen($row[2]);
                        $nationalCode = $row[2];
                        if ($nationalCodeLength <= 10) {
                            $nationalCode = str_pad($nationalCode, 10, '0', STR_PAD_LEFT);
                        }
                        $customerQuery->where('national_code', $nationalCode)
                            ->orWhere('company_national_code', $nationalCode);
                    });
            })
            ->get()
            ->first();
        if (is_null($profile)) {
            $this->setProfile($row);
            $this->setCustomer($row);
            $this->setBusiness($row);
            $this->setAccount($row);
            $this->setTerminal($row);
        } else {
            $this->profile = $profile;
            $terminal = Terminal::where('terminal_number', $row[8])->get()->first();
            if (is_null($terminal)) {
                if (!is_null($row[14]) && count($profile->accounts) > 0 && !is_null($profile->accounts->first()->account) && $row[14] != $profile->accounts->first()->account->account_number) {
                    $this->setAccount($row);
                }
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
        $status = 8;

        $this->profile = Profile::create([
            'type' => self::PROFILE_TYPE,
            'user_id' => self::USER_ID,
            'psp_id' => self::PSP_ID,
            'merchant_id' => $row[9],
            'status' => $status,
            'licenses_status' => 0,
            'device_physical_status' => 'new',
        ]);
        if (!is_null($row[31])) $this->profile->created_at = Jalalian::fromFormat('Y/m/d', substr($row[31], 0, 10))->toCarbon();
        if (!is_null($row[28])) $this->profile->updated_at = Jalalian::fromFormat('Y/m/d', substr($row[28], 0, 10))->toCarbon();
        $this->profile->save();
    }

    private function setCustomer(array $row)
    {
        $firstName = 'نامشخص';
        $lastName = 'نامشخص';
        $nationalCodeLength = strlen($row[2]);
        if ($nationalCodeLength <= 10) {
            $type = 'PERSON';
            $row[2] = str_pad($row[2], 10, '0', STR_PAD_LEFT);
        } else {
            $type = 'ORGANIZATION';
        }
        if (!is_null($row[1])) {
            $name = explode(' ', $row[1]);
            $firstName = $name[0];
            $lastName = '';
            for ($i = 1; $i < count($name); $i++) {
                $lastName .= ' ' . $name[$i];
            }
        }

        $mobile = 'نامشخص';
        if (!is_null($row[6])) {
            $mobile = str_replace('-', '', $row[6]);
            if (substr($mobile, 0, 1) != '0') {
                $mobile = '0' . $mobile;
            }
        }

        $this->profile->customer()->create([
            'type' => $type,
            'user_id' => self::USER_ID,
            'national_code' => is_null($row[2]) ? '-' : $row[2],
            'id_code' => is_null($row[2]) ? '-' : $row[2],
            'first_name' => $firstName,
            'first_name_english' => null,
            'last_name' => $lastName,
            'last_name_english' => null,
            'father' => null,
            'father_english' => null,
            'gender' => 'male',
            'mobile' => $mobile,
            'birthday' => '2020-01-01',
            'company_name' => $type === 'ORGANIZATION' ? $row[1] : null,
            'company_name_english' => null,
            'business_name' => null,
            'reg_date' => null,
            'reg_code' => null,
            'company_national_code' => $type === 'ORGANIZATION' && !is_null($row[2]) ? $row[2] : '-',
        ]);
    }

    private function setBusiness(array $row)
    {
        $ostan = DB::table('ostan')->where('name', $row[23])->select()->get(['id', 'name'])->first();
        $city = DB::table('shahr')->where('name', $row[24])->select()->get(['id', 'name'])->first();


        $phoneCode = null;
        $phone = $row[16];

        $this->profile->business()->create([
            'profile_id' => $this->profile->id,
            'ostan_id' => is_null($ostan) ? 22 : $ostan->id,
            'shahrestan_id' => null,
            'bakhsh_id' => null,
            'shahr_id' => is_null($city) ? 1142 : $city->id,
            'dehestan_id' => null,
            'abadi_id' => null,
            'phone_code' => $phoneCode,
            'senf' => 'نامشخص',
            'name' => $row[21],
            'name_english' => \Illuminate\Support\Str::slug($row[21]),
            'address' => null,
            'postal_code' => $row[17],
            'phone' => $phone,
            'tax_code' => null,
            'has_license' => 'NO',
            'license_code' => null,
            'license_date' => null
        ]);
    }

    private function setAccount(array $row)
    {
        $bank = Bank::where('name', $row[15])->get()->first();

        $account = Account::create([
            'customer_id' => $this->profile->customer->id,
            'bank_id' => is_null($bank) ? 34 : $bank->id,
            'branch' => 'نامشخص',
            'account_number' => is_null($row[14]) ? 'نامشخص' : str_replace(['-', '_', '.', ' '], '', $row[14]),
            'sheba_code' => is_null($row[15]) ? 'نامشخص' : str_replace(['-', '_', '.', ' '], '', $row[15]),
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
        if (!is_null($row[7])) {
            $deviceType = DeviceType::where('name', $row[7])->get()->first();
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
        if (!is_null($row[5])) {
            $device = Device::firstOrCreate([
                'serial' => $row[5]
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
            'terminal_number' => $row[8],
            'device_type_id' => $deviceTypeId,
            'device_connection_type_id' => $deviceConnectionTypeId,
            'device_id' => $deviceId,
            'status' => $status,
        ]);
    }
}
