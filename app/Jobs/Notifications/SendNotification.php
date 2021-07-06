<?php

namespace App\Jobs\Notifications;

use App\Models\Notifications\BatchNotification;
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
    protected string $reception;
    protected array $values;

    /**
     * SendNotification constructor.
     * @param BatchNotification $notification
     * @param string $reception
     * @param string $apiCode
     */
    public function __construct(BatchNotification $notification, string $reception, string $apiCode = 'RwoB81G8VWdrZ4xc-GmNp96xPlk1rvdcYmUGnSCvWZY=')
    {
        $this->notification = $notification;
        $this->reception = $reception;
        $this->apiCode = $apiCode;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new  Client($this->apiCode);
        try {
            $bulkId = $client->sendPattern(trim($this->notification->pattern), '+985000125475', $this->reception, $this->notification->parametersList);
            Log::channel('notifications')->info('Bulk Id:' . $bulkId . ' - - - - Reception: ' . $this->reception . ' - - - - Pattern: ' . $this->notification->pattern);
        } catch (Error $e) {
            Log::channel('notifications')->error('Error: ' . json_encode($e->unwrap()));
        } catch (HttpException $e) {
            Log::channel('notifications')->error('Exception: ' . $e->getMessage());
        }
    }
}
