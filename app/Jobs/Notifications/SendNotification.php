<?php

namespace App\Jobs\Notifications;

use App\Models\Notifications\BatchNotification;
use App\Models\Notifications\Reception;
use Cryptommer\Smsir\Objects\VerifyResponse;
use Cryptommer\Smsir\Smsir;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use IPPanel\Client;
use IPPanel\Errors\Error;
use IPPanel\Errors\HttpException;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $apiCode;
    protected BatchNotification $notification;
    protected string $pattern;
    protected string $type;
    protected string $reception;
    protected array $receptions;
    protected array $values;

    /**
     * SendNotification constructor.
     * @param BatchNotification $notification
     * @param string $reception
     * @param array $receptions
     * @param string $type
     * @param string $apiCode
     */
    public function __construct(BatchNotification $notification, string $reception = '', array $receptions = [], string $type = 'pattern', string $apiCode = 'IRE7mq1Ht-kaB2d_4lF5EIXByN-hS1AsUIuQiLmpSas=')
    {
        $this->notification = $notification;
        $this->reception = $reception;
        $this->receptions = $receptions;
        $this->apiCode = $apiCode;
        $this->type = $type;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->type === 'pattern') {
            $this->sendByPattern();
        } elseif ($this->type === 'club') {
            $this->sendByClub();
        }
    }

    private function sendByPattern()
    {
//        $client = new Client($this->apiCode);
        $client = smsir::Send();
        $parameters = [];
        foreach ($this->notification->parametersList as $key => $value) {
            $parameters[] = new \Cryptommer\Smsir\Objects\Parameters($key, $value);
        }
        /**
         * @var  VerifyResponse $response
         */
        try {
            $response = $client->Verify($this->reception, $this->notification->pattern, $parameters);
            if ($response && $response->getStatus() == 1) {
                $reception = Reception::where('batch_notification_id', $this->notification->id)->where('reception', $this->reception)->get()->first();
                if (!is_null($reception)) {
                    $reception->status = $response->getStatus();
                    $reception->save();
                }
            }
        } catch (\Exception $exception) {
            Log::channel('notifications')->error($exception->getMessage());
        }
    }

    private function sendByClub()
    {
        $client = new  Client($this->apiCode);
        try {
            $bulkId = $client->send('+9810008331420000', $this->receptions, $this->notification->body);
//            $this->notification->bulk_id = $bulkId;
//            $this->notification->save();
            Log::channel('notifications')->info('Bulk Id:' . $bulkId . ' - - - - Receptions count: ' . count($this->receptions) . ' - - - - id: ' . $this->notification->id);
        } catch (Error $e) {
            Log::channel('notifications')->error('Error: ' . json_encode($e->unwrap()));
        } catch (HttpException $e) {
            Log::channel('notifications')->error('Exception: ' . $e->getMessage());
        }
    }
}
