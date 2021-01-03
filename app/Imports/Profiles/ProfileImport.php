<?php

namespace App\Imports\Profiles;

use App\Models\Profiles\Account;
use App\Models\Profiles\Business;
use App\Models\Profiles\Customer;
use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfilesAccount;
use App\Models\User;
use App\Models\Variables\Bank;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Morilog\Jalali\Jalalian;

class ProfileImport implements ToModel, WithStartRow
{
    private $user;

    /**
     * ProfileImport constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function model(array $row)
    {
        $createdAt = now()->toDateTimeString();
        if (!is_null($row[0])) {
            $createdAt = $jDate = Jalalian::fromFormat('Y/m/d', $row[0])->toCarbon()->toDateTimeString();
        }
        $updatedAtAt = now()->toDateTimeString();
        if (!is_null($row[1])) {
            $updatedAtAt = $jDate = Jalalian::fromFormat('Y/m/d', $row[0])->toCarbon()->toDateTimeString();
        }

        $user = $this->user;
        if (!is_null($row[2])) {
            $user = User::where('name', $row[2])->get()->first();
            if (is_null($user)) $user = $this->user;
        }

        $status = $this->getStatus($row[3]);

        $psp = Psp::where('name', $row[4])->get()->first();
        if (is_null($psp)) return;

        $deviceType = DeviceType::where('name', $row[5])->get()->first();
        if (is_null($deviceType)) return;

        $serial = Device::where('serial', $row[6])->get()->first();

        $ostan = DB::table('ostan')->where('name', $row[27])->get()->first();
        if (is_null($ostan)) return;
        $shahrestan = DB::table('shahrestan')->where('name', $row[28])->get()->first();
        if (is_null($shahrestan)) return;
        $bakhsh = DB::table('bakhsh')->where('name', $row[29])->get()->first();
        if (is_null($bakhsh)) return;
        $shahr = DB::table('shahr')->where('name', $row[30])->get()->first();
        if (is_null($shahr)) return;


        $profile = Profile::create([
            'user_id' => $user->id,
            'psp_id' => $psp->id,
            'device_type_id' => $deviceType->id,
            'device_id' => is_null($serial) ? '' : $serial->id,
            'terminal_id' => $row[7],
            'merchant_id' => $row[8],
            'status' => $status,
            'created_at' => $createdAt,
            'updated_at' => $updatedAtAt
        ]);
        /*
         * اطلاعات مشتری
         */
        $customer = Customer::create([
            'type' => $row[9] == 'حقیقی' ? 'PERSON' : 'ORGANIZATION',
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'national_code' => $row[16],
            'id_code' => $row[17],
            'first_name' => $row[10],
            'first_name_english' => $row[11],
            'last_name' => $row[12],
            'last_name_english' => $row[13],
            'father' => $row[14],
            'father_english' => $row[15],
            'gender' => $row[18] == 'مرد' ? 'male' : 'female',
            'mobile' => $row[19],
            'birthday' => str_replace('/', '-', $row[20]),
            'company_name' => $row[21],
            'company_name_english' => $row[22],
            'business_name' => $row[23],
            'reg_date' => is_null($row[24]) ? null : str_replace('/', '-', $row[24]),
            'reg_code' => $row[25],
            'company_national_code' => $row[26]
        ]);

        $business = Business::create([
            'profile_id' => $profile->id,
            'ostan_id' => $ostan->id,
            'shahrestan_id' => $shahrestan->id,
            'bakhsh_id' => $bakhsh->id,
            'shahr_id' => $shahr->id,
            'phone_code' => substr($row[31], 0, 3),
            'senf' => $row[32],
            'name' => $row[33],
            'name_english' => $row[34],
            'address' => $row[35],
            'postal_code' => $row[36],
            'phone' => substr($row[31], 3),
            'has_license' => $row[37] == 'خیر' ? 'NO' : 'YES',
            'license_code' => $row[38],
            'license_date' => $row[39]
        ]);

        for ($i = 40; $i < 95; $i = $i + 9) {
            $j = $i + 1;
            $k = $i + 2;
            $m = $i + 3;
            $n = $i + 4;
            $o = $i + 5;
            $p = $i + 6;
            $q = $i + 7;
            $r = $i + 8;
            if ((key_exists($i, $row) && !is_null($row[$i])) &&
                (key_exists($j, $row) && !is_null($row[$j])) &&
                (key_exists($k, $row) && !is_null($row[$k])) &&
                (key_exists($m, $row) && !is_null($row[$m])) &&
                (key_exists($n, $row) && !is_null($row[$n])) &&
                (key_exists($o, $row) && !is_null($row[$o])) &&
                (key_exists($p, $row) && !is_null($row[$p])) &&
                (key_exists($q, $row) && !is_null($row[$q])) &&
                (key_exists($r, $row) && !is_null($row[$r]))
            ) {
                $this->createAccount($profile->id, $customer->id, $row[$i], $row[$j], $row[$k], $row[$m], $row[$n], $row[$o], $row[$p], $row[$q], $row[$r]);
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    private function getStatus($status)
    {
        switch ($status) {
            case 'ثبت شده':
                return 1;
                break;
            case 'در انتظار بررسی مدارک':
                return 2;
                break;
            case 'تایید مدارک':
                return 3;
                break;
            case 'ثبت در PSP':
                return 4;
                break;
            case 'تایید شاپرک':
                return 5;
                break;
            case 'در انتظار تخصیص':
                return 6;
                break;
            case 'تخصیص داده شده':
                return 7;
                break;
            default:
            case 'نصب شده':
                return 8;
                break;
            case 'ابطال':
                return 9;
                break;
            case 'عدم تایید مدارک':
                return 10;
                break;
            case 'عدم تایید شاپرک':
                return 11;
                break;
            case 'درخواست ابطال':
                return 12;
                break;
            case 'عدم تایید سریال':
                return 13;
                break;
            case 'درخواست انتقال':
                return 14;
                break;
            case 'رد درخواست انتقال':
                return 15;
                break;
            case 'ثبت موقت':
                return 0;
                break;

        }
    }

    private function createAccount($profileId, $customerId, $bankName, $branch, $account_number, $sheba_code, $first_name, $last_name, $national_code, $mobile, $birthday)
    {
        $bank = Bank::where('name', $bankName)->get()->first();
        if (is_null($bank)) return;
        $account = Account::create([
            'customer_id' => $customerId,
            'bank_id' => $bank->id,
            'branch' => $branch,
            'account_number' => $account_number,
            'sheba_code' => substr($sheba_code, '2'),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'national_code' => $national_code,
            'mobile' => $mobile,
            'birthday' => str_replace('/', '-', $birthday)
        ]);

        ProfilesAccount::create([
            'profile_id' => $profileId,
            'account_id' => $account->id
        ]);
    }
}
