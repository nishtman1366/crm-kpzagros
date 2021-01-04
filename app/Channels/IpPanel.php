<?php


namespace App\Channels;

use App\Exceptions\NotificationException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;

class IpPanel
{
    public $apiKey;
    public $originator;

    /**
     * Smsam constructor.
     * @param $apiKey
     * @param $originator
     */
    public function __construct($apiKey = 'nu6BzocZfeV8m0j20nMoyKwIS-8BLliWHEi0uhHWQ14=', $originator = '+985000125475')
    {
        $this->apiKey = systemConfig('SMS_API_TOKEN', $apiKey);
        $this->originator = systemConfig('SMS_ORIGINATOR', $originator);
    }

    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notifiable, 'routeNotificationForIpPanel')) {
            $id = $notifiable->routeNotificationForIpPanel($notifiable);
        } else {
            $id = $notifiable->mobile;
        }

        $data = (method_exists($notification, 'toIpPanel')) ?
            $notification->toIpPanel($notifiable) :
            $notification->toArray($notifiable);

        if (empty($data)) return false;

        try {
            $bulkId = $this->sendSms($data['patternCode'], $data['patternValues'], $id);
            if ($bulkId == 0) {
                throw new NotificationException('خطا در ارسال پیامک');
            }
            return true;
        } catch (NotificationException $e) {
            return false;
        }
    }

    /**
     * @param $patternCode
     * @param $patternValues
     * @param $mobile
     * @return int
     * @throws NotificationException
     */
    public function sendSms($patternCode, $patternValues, $mobile)
    {
        $bulkId = 0;
        $client = new Client($this->apiKey);
//        $credit = $client->getCredit();
        try {
            $bulkId = $client->sendPattern($patternCode, $this->originator, $mobile, $patternValues);
        } catch (Error $e) {
            throw new NotificationException(json_encode($e->unwrap()));
        } catch (HttpException $e) {
            throw new NotificationException(json_encode($e->getMessage()));
        }

        return $bulkId;
    }
}
