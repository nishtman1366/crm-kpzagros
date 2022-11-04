<?php

namespace App\Libraries\Payments\ZarinPal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class ZarinPal
{
    private string $baseUrl = 'https://www.zarinpal.com/pg/rest/WebGate/';
    private string $merchantID;
    public string $redirectUrl = 'https://www.zarinpal.com/pg/StartPay/%s';
    public $Authority;

    public function __construct($merchantID)
    {
        $this->merchantID = $merchantID;
    }

    public function request($callbackURL, $Amount, $Description, $Email = null, $Mobile = null, $additionalData = null)
    {
        $inputs = [
            'MerchantID' => $this->merchantID,
            'CallbackURL' => $callbackURL,
            'Amount' => $Amount,
            'Description' => $Description,
        ];
        if (!is_null($Email)) {
            $inputs['Email'] = $Email;
        }
        if (!is_null($Mobile)) {
            $inputs['Mobile'] = $Mobile;
        }
        if (!is_null($additionalData)) {
            $inputs['AdditionalData'] = $additionalData;
            $result = $this->call('PaymentRequestWithExtra.json', $inputs);
            if ($result['Status'] == 100) {
                $results = ['Authority' => $result['Authority']];
            } else {
                $results = [
                    'Status' => 'error',
                    'error' => !empty($result['Status']) ? $result['Status'] : null,
                    'errorInfo' => !empty($result['errors']) ? $result['errors'] : null,
                ];
            }
        } else {
            $result = $this->call('PaymentRequest.json', $inputs);
            if ($result['Status'] == 100) {
                $results = ['Authority' => $result['Authority']];
            } else {
                $results = ['error' => $result['Status']];
            }
        }

        if (empty($results['Authority'])) {
            $results['Authority'] = null;
        }
        $this->Authority = $results['Authority'];

        return $results;
    }

    public function verify($amount, $authority)
    {
        $inputs = [
            'MerchantID' => $this->merchantID,
            'Authority' => $authority,
            'Amount' => $amount,
        ];

        $result = $this->call('PaymentVerification.json', $inputs);
        if ($result['Status'] == 100) {
            return [
                'Status' => 'success',
                'RefID' => $result['RefID'],
            ];
        } elseif ($result['Status'] == 101) {
            return [
                'Status' => 'verified_before',
                'RefID' => $result['RefID'],
            ];
        } else {
            return [
                'Status' => 'error',
                'error' => !empty($result['Status']) ? $this->getStatusMessage($result['Status']) : null,
                'errorInfo' => !empty($result['errors']) ? $result['errors'] : null,
            ];
        }
    }

    private function call($uri, $data)
    {
        try {
            $client = new Client(['base_uri' => $this->baseUrl]);
            $response = $client->request('POST', $uri, ['json' => $data]);

            $rawBody = $response->getBody()->getContents();
            $body = json_decode($rawBody, true);
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $rawBody = is_null($response) ? '{"Status":-98,"message":"http connection error"}' : $response->getBody()->getContents();
            $body = json_decode($rawBody, true);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            $rawBody = is_null($response) ? '{"Status":-98,"message":"http connection error"}' : $response->getBody()->getContents();
            $body = json_decode($rawBody, true);
        }

        if (!isset($result['Status'])) {
            $result['Status'] = -99;
        }

        return $body;
    }

    public function redirect()
    {
        header('Location: ' . sprintf($this->redirectUrl, $this->Authority));
        die;
    }


    /**
     * active sandbox mod for test env.
     */

    public function enableSandbox()
    {
        $this->baseUrl = 'https://sandbox.zarinpal.com/pg/rest/WebGate/';
    }

    /**
     * active zarinGate mode.
     */
    public function isZarinGate()
    {
        $this->redirectUrl = $this->redirectUrl . '/ZarinGate';
    }

    private function getStatusMessage($status)
    {
        switch ($status) {
            case -9:
                return 'خطای اعتبار سنجی';
            case -10:
                return 'ای پی و يا مرچنت كد پذيرنده صحيح نيست';
            case -11:
                return 'مرچنت کد فعال نیست لطفا با تیم پشتیبانی ما تماس بگیرید';
            case -12:
                return 'تلاش بیش از حد در یک بازه زمانی کوتاه.';
            case -15:
                return 'ترمینال شما به حالت تعلیق در آمده با تیم پشتیبانی تماس بگیرید';
            case -16:
                return 'سطح تاييد پذيرنده پايين تر از سطح نقره اي است.';
            case 100:
                return 'عملیات موفق';
            case -30:
                return 'اجازه دسترسی به تسویه اشتراکی شناور ندارید';
            case -31:
                return 'حساب بانکی تسویه را به پنل اضافه کنید مقادیر وارد شده واسه تسهیم درست نیست';
            case -32:
                return 'Wages is not valid, Total wages(floating) has been overload max amount.';
            case -33 :
                return 'درصد های وارد شده درست نیست';
            case -34 :
                return 'مبلغ از کل تراکنش بیشتر است';
            case -35:
                return 'تعداد افراد دریافت کننده تسهیم بیش از حد مجاز است';
            case -40:
                return 'Invalid extra params, expire_in is not valid.';
            case -50 :
                return 'مبلغ پرداخت شده با مقدار مبلغ در وریفای متفاوت است';
            case -51:
                return 'پرداخت ناموفق';
            case -52:
                return 'خطای غیر منتظره با پشتیبانی تماس بگیرید';
            case -53:
                return 'اتوریتی برای این مرچنت کد نیست';
            case -54:
                return 'اتوریتی نامعتبر است';
            case 101:
                return 'تراکنش وریفای شده';
        }
    }
}
