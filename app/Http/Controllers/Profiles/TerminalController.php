<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\Profile;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use niklasravnsborg\LaravelPdf\PdfWrapper;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TerminalController extends Controller
{
    public function update(Profile $profile, Terminal $terminal, Request $request)
    {
        $validationArray = [];
        $actions = ['new', 'confirm', 'terminal', 'reject', 'install', 'change', 'changeSerial', 'confirmChange', 'cancelChange', 'cancel', 'confirmCancel', 'cancelCancel'];
        $request->validateWithBag('terminalForm', [
            'action' => 'required|in:' . implode(',', $actions)
        ]);
        if ($terminal->profile_id !== $profile->id) throw new UnauthorizedHttpException('', 'ترمینال به پرونده تعلق ندارد.');

        $action = $request->get('action', 'new');
        $terminalData = [];
        $profileData = [];
        $messageStatus = null;
        $message = null;
        switch ($action) {
            default:
            case 'new':
                /*
                * انتخاب سریال
                */
                $request->validateWithBag('terminalForm', [
                    'terminal.device_id' => 'required|exists:devices,id'
                ]);
                $device = Device::find($request->get('terminal')['device_id']);
                $device->transport_status = 2;
                $device->psp_status = 1;
                $device->save();
                $messageStatus = 6;
                break;
            case 'terminal':
                /*
                * ثبت شماره ترمینال
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.terminal_number' => 'required|unique:terminals,terminal_number'
                ]);
                $device = Device::find($request->get('terminal')['device_id']);
                $device->transport_status = 2;
                $device->psp_status = 2;
                $device->save();
                $messageStatus = 7;
                break;
            case 'reject':
                /*
                * رد سریال انتخاب شده
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.reject_reason' => 'required'
                ]);

                $terminal->device->transport_status = 1;
                $terminal->device->psp_status = 1;
                $terminal->device->save();
                $messageStatus = 13;
                break;
            case 'install':
                /*
                  * نصب دستگاه
                 */
                $terminal->setup_date = now();
                $terminal->device->transport_status = 3;
                $terminal->device->psp_status = 2;
                $terminal->device->save();

                $messageStatus = 8;
                break;
            case 'change':
                /*
                 * درخواست جابجایی
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.change_reason' => 'required',
                    'terminal.new_device_type_id' => 'required',
                ]);
                $messageStatus = 14;
                break;
            case 'changeSerial':
                $request->validateWithBag('terminalForm', [
                    'terminal.device_id' => 'required|exists:devices,id'
                ]);
                $device = Device::find($request->get('terminal')['new_device_id']);
                $device->transport_status = 4;
                $device->psp_status = 1;
                $device->save();
                $messageStatus = 15;
                break;
            case 'confirmChange':
                /*
                 * تایید جابجایی
                 */
                $terminal->device->transport_status = 1;
                $terminal->device->psp_status = 1;
                $terminal->device->save();

                $device = Device::find($request->get('terminal')['device_id']);
                $device->transport_status = 2;
                $device->psp_status = 2;
                $device->save();

                $messageStatus = 17;
                break;
            case 'cancelChange':
                /*
                 * رد جابجایی
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.reject_reason' => 'required'
                ]);

                $device = Device::find($request->get('terminal')['reserved_device_id']);
                $device->transport_status = 1;
                $device->psp_status = 1;
                $device->save();

                $messageStatus = 16;
                break;
            case 'cancel':
                /*
                 * درخواست ابطال
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.cancel_reason' => 'required',
                ]);
                break;
            case 'confirmCancel':
                /*
                 * تایید ابطال
                 */

                $device = Device::find($request->get('terminal')['reserved_device_id']);
                $device->transport_status = 1;
                $device->psp_status = 1;
                $device->save();
                $messageStatus = 9;
                break;
            case 'cancelCancel':
                /*
                 * رد ابطال
                 */
                $request->validateWithBag('terminalForm', [
                    'terminal.reject_reason' => 'required'
                ]);
                $message = $request->get('terminal')['reject_reason'];
                $messageStatus = 18;
                break;
        }

        $terminal->fill($request->get('terminal'));
        $terminal->save();

        $profile->fill($request->get('profile'));
        $profile->save();

        $user = Auth::user();
        setProfileMessage($messageStatus, $user, $profile, $message);

        return redirect()->back();
    }

    public function deliveryForm(Profile $profile, Terminal $terminal)
    {
        $profile->load('customer', 'user', 'psp', 'business',
            'accounts', 'accounts.account', 'accounts.account.bank', 'messages', 'licenses', 'licenses.type');
        $terminal->load('device', 'deviceType');

        $pdfWrapper = new PdfWrapper();
        $pdf = $pdfWrapper->loadView('pdf.profile_deliver_form', compact('profile', 'terminal'), [], [
            'mode' => 'utf-8',
        ]);
        $fileName = is_null($profile->customer) ? 'deliveryForm.pdf' : Str::slug($profile->customer->fullName) . '.pdf';
        return $pdf->download($fileName);
    }
}
