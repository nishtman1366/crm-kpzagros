<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class NotificationException extends Exception
{

    /**
     * NotificationException constructor.
     * @param $message
     */
    public function __construct($message = null)
    {
        parent::__construct();
        Log::channel('notifications')->error(!is_null($message) ? $message : 'اطلاعات اعلان یافت نشد.');
    }
}
