<?php


namespace App\Channels;

use App\Exceptions\NotificationException;
use Cryptommer\Smsir\Objects\VerifyResponse;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsIr
{
    /**
     * Sends a notification to the specified notifiable entity
     *
     * @param mixed $notifiable The notifiable entity to send the notification to
     * @param Notification $notification The notification to be sent
     * @return bool Returns true if the notification is sent successfully, false otherwise
     */
    public function send($notifiable, Notification $notification): bool
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
            $this->sendByPattern($data['patternCode'], $data['patternValues'], $id);
            return true;
        } catch (NotificationException $e) {
            Log::channel('daily')->error($e->getMessage());
            return false;
        }
    }

    /**
     * Sends a message using a specified pattern code and values to a mobile number
     *
     * @param int $patternCode The pattern code to use for sending the message
     * @param array $patternValues An array of key-value pairs representing the values to be replaced in the pattern
     * @param string $mobile The mobile number to send the message to
     * @throws NotificationException If there is a connection error
     */
    private function sendByPattern(int $patternCode, array $patternValues, string $mobile)
    {
        $client = \Cryptommer\Smsir\Smsir::Send();
        $parameters = [];
        foreach ($patternValues as $key => $value) {
            $parameters[] = new \Cryptommer\Smsir\Objects\Parameters($key, $value);
        }
        /**
         * @var  VerifyResponse $response
         */
        try {
            $response = $client->Verify($mobile, $patternCode, $parameters);
            if ($response && $response->getStatus() == 1) {
                Log::channel('notifications')->error(json_encode($response));
            } else {
                throw new NotificationException('Connection Error');
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $message = $e->getMessage();
            if ($e->getResponse()) {
                $result = json_decode($e->getResponse()->getBody());
                $message = $result->message;
            }
            Log::channel('notifications')->error('###########################');
            Log::channel('notifications')->error('System Message: ' . $message);
            Log::channel('notifications')->error('Pattern: ' . $patternCode);
            Log::channel('notifications')->error('Mobile Number: ' . $mobile);
            Log::channel('notifications')->error('Parameters: ' . json_encode($patternValues));
            Log::channel('notifications')->error('###########################');
        }
    }

}
