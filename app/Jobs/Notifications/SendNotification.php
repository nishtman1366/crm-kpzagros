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
    public function __construct(BatchNotification $notification, string $reception = '', array $receptions = [], string $type = 'pattern', string $apiCode = 'RwoB81G8VWdrZ4xc-GmNp96xPlk1rvdcYmUGnSCvWZY=')
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
    public
    function handle()
    {
        if ($this->type === 'pattern') {
            $this->sendByPattern();
        } elseif ($this->type === 'club') {
            $this->sendByClub();
        }
    }

    private
    function sendByPattern()
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

    private function sendByClub()
    {
        $client = new  Client($this->apiCode);
        try {
            $bulkId = $client->send('+98club', $this->receptions, $this->notification->body);
            Log::channel('notifications')->info('Bulk Id:' . $bulkId . ' - - - - Receptions count: ' . count($this->receptions) . ' - - - - id: ' . $this->notification->id);
        } catch (Error $e) {
            Log::channel('notifications')->error('Error: ' . json_encode($e->unwrap()));
        } catch (HttpException $e) {
            Log::channel('notifications')->error('Exception: ' . $e->getMessage());
        }
    }
}
